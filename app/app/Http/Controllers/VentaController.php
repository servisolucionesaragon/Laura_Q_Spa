<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Tratamiento;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\VentaPago;
use App\Models\MovimientoStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $desde = $request->get('desde', now()->subDays(30)->format('Y-m-d'));
        $hasta = $request->get('hasta', now()->format('Y-m-d'));

        $ventas = Venta::with(['cliente', 'user'])
            ->whereBetween('fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderByDesc('fecha')
            ->paginate(20)->withQueryString();

        $totales = [
            'count'  => Venta::whereBetween('fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->where('estado', 'pagada')->count(),
            'total'  => Venta::whereBetween('fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->where('estado', 'pagada')->sum('total'),
        ];

        return view('ventas.index', compact('ventas', 'desde', 'hasta', 'totales'));
    }

    public function tpv(): View
    {
        return view('ventas.tpv', [
            'tratamientos' => Tratamiento::activos()->orderBy('nombre')->get(),
            'productos'    => Producto::activos()->paraVenta()->orderBy('nombre')->get(),
            'clientes'     => Cliente::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido', 'telefono']),
            'profesionales'=> User::where('rol', 'profesional')->where('activo', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cliente_id'           => 'nullable|exists:clientes,id',
            'metodo_pago'          => 'required|in:efectivo,tarjeta,transferencia,mixto,otro',
            'descuento'            => 'nullable|numeric|min:0',
            'notas'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.tipo'         => 'required|in:servicio,producto,bono,otro',
            'items.*.referencia_id'=> 'nullable|integer',
            'items.*.profesional_id'=> 'nullable|exists:users,id',
            'items.*.descripcion'  => 'required|string|max:191',
            'items.*.cantidad'     => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $impIncluido = (bool) ($GLOBALS['configEmpresa']->impuesto_incluido ?? true);
        $impPct = (float) (optional(\App\Models\ConfiguracionEmpresa::first())->impuesto_porcentaje ?? 12);

        $venta = DB::transaction(function () use ($datos, $impIncluido, $impPct) {
            $subtotal = 0;
            foreach ($datos['items'] as $it) {
                $subtotal += (float) $it['cantidad'] * (float) $it['precio_unitario'];
            }
            $descuento = (float) ($datos['descuento'] ?? 0);
            $base = max(0, $subtotal - $descuento);
            $impuesto = $impIncluido ? 0 : round($base * ($impPct / 100), 2);
            $total = $base + $impuesto;

            $venta = Venta::create([
                'numero'      => Venta::generarNumero(),
                'cliente_id'  => $datos['cliente_id'] ?? null,
                'user_id'     => auth()->id(),
                'fecha'       => now(),
                'subtotal'    => $subtotal,
                'descuento'   => $descuento,
                'impuesto'    => $impuesto,
                'total'       => $total,
                'metodo_pago' => $datos['metodo_pago'],
                'estado'      => 'pagada',
                'notas'       => $datos['notas'] ?? null,
            ]);

            foreach ($datos['items'] as $it) {
                $sub = (float) $it['cantidad'] * (float) $it['precio_unitario'];
                VentaItem::create([
                    'venta_id'        => $venta->id,
                    'tipo'            => $it['tipo'],
                    'referencia_id'   => $it['referencia_id'] ?? null,
                    'profesional_id'  => $it['profesional_id'] ?? null,
                    'descripcion'     => $it['descripcion'],
                    'cantidad'        => $it['cantidad'],
                    'precio_unitario' => $it['precio_unitario'],
                    'subtotal'        => $sub,
                ]);

                // Descontar stock si es producto
                if ($it['tipo'] === 'producto' && ! empty($it['referencia_id'])) {
                    $producto = Producto::find($it['referencia_id']);
                    if ($producto && $producto->stock_actual > 0) {
                        $cant = (int) $it['cantidad'];
                        $anterior = $producto->stock_actual;
                        $nuevo = max(0, $anterior - $cant);
                        $producto->update(['stock_actual' => $nuevo]);
                        MovimientoStock::create([
                            'producto_id'    => $producto->id,
                            'tipo'           => 'salida',
                            'cantidad'       => $cant,
                            'stock_anterior' => $anterior,
                            'stock_nuevo'    => $nuevo,
                            'motivo'         => 'Venta ' . $venta->numero,
                            'referencia'     => 'venta:' . $venta->id,
                            'user_id'        => auth()->id(),
                        ]);
                    }
                }
            }

            VentaPago::create([
                'venta_id' => $venta->id,
                'metodo'   => $datos['metodo_pago'] === 'mixto' ? 'efectivo' : $datos['metodo_pago'],
                'monto'    => $total,
            ]);

            return $venta;
        });

        return redirect()->route('ventas.show', $venta)->with('success', 'Venta registrada correctamente.');
    }

    public function show(Venta $venta): View
    {
        $venta->load(['cliente', 'user', 'items', 'pagos']);
        return view('ventas.show', compact('venta'));
    }

    public function anular(Venta $venta): RedirectResponse
    {
        if ($venta->estado === 'anulada') {
            return back()->with('error', 'Esta venta ya está anulada.');
        }
        $venta->update(['estado' => 'anulada']);
        return back()->with('success', 'Venta anulada.');
    }
}
