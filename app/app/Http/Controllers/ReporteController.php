<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function ventas(Request $request): View
    {
        $desde = $request->get('desde', Carbon::now()->subDays(30)->format('Y-m-d'));
        $hasta = $request->get('hasta', Carbon::now()->format('Y-m-d'));

        $rango = [$desde . ' 00:00:00', $hasta . ' 23:59:59'];

        $resumen = [
            'total'        => (float) Venta::whereBetween('fecha', $rango)->where('estado', 'pagada')->sum('total'),
            'cantidad'     => Venta::whereBetween('fecha', $rango)->where('estado', 'pagada')->count(),
            'ticket_promedio' => 0.0,
            'descuentos'   => (float) Venta::whereBetween('fecha', $rango)->where('estado', 'pagada')->sum('descuento'),
        ];
        if ($resumen['cantidad'] > 0) $resumen['ticket_promedio'] = $resumen['total'] / $resumen['cantidad'];

        // Ventas por día
        $porDia = Venta::selectRaw('DATE(fecha) as dia, SUM(total) as total, COUNT(*) as cantidad')
            ->whereBetween('fecha', $rango)->where('estado', 'pagada')
            ->groupBy('dia')->orderBy('dia')->get();

        // Por método
        $porMetodo = Venta::selectRaw('metodo_pago, SUM(total) as total, COUNT(*) as cantidad')
            ->whereBetween('fecha', $rango)->where('estado', 'pagada')
            ->groupBy('metodo_pago')->get();

        // Top servicios
        $topServicios = VentaItem::selectRaw("venta_items.descripcion, venta_items.tipo, SUM(venta_items.cantidad) as cantidad, SUM(venta_items.subtotal) as total")
            ->join('ventas', 'ventas.id', '=', 'venta_items.venta_id')
            ->whereBetween('ventas.fecha', $rango)->where('ventas.estado', 'pagada')
            ->where('venta_items.tipo', 'servicio')
            ->groupBy('venta_items.descripcion', 'venta_items.tipo')
            ->orderByDesc('total')->limit(10)->get();

        // Top productos
        $topProductos = VentaItem::selectRaw("venta_items.descripcion, SUM(venta_items.cantidad) as cantidad, SUM(venta_items.subtotal) as total")
            ->join('ventas', 'ventas.id', '=', 'venta_items.venta_id')
            ->whereBetween('ventas.fecha', $rango)->where('ventas.estado', 'pagada')
            ->where('venta_items.tipo', 'producto')
            ->groupBy('venta_items.descripcion')
            ->orderByDesc('total')->limit(10)->get();

        // Top profesionales
        $topProfesionales = Cita::selectRaw('profesional_id, COUNT(*) as citas, SUM(total) as total')
            ->with('profesional:id,name')
            ->whereBetween('fecha', [$desde, $hasta])->where('estado', 'realizada')
            ->whereNotNull('profesional_id')
            ->groupBy('profesional_id')->orderByDesc('total')->limit(10)->get();

        return view('reportes.ventas', compact(
            'desde', 'hasta', 'resumen', 'porDia', 'porMetodo',
            'topServicios', 'topProductos', 'topProfesionales'
        ));
    }

    public function indicadores(): View
    {
        $hoy = Carbon::today();
        $mes = now()->month;
        $ano = now()->year;

        $kpis = [
            'clientes_total'    => Cliente::count(),
            'clientes_activos'  => Cliente::where('activo', true)->count(),
            'clientes_mes'      => Cliente::whereMonth('created_at', $mes)->whereYear('created_at', $ano)->count(),
            'citas_mes'         => Cita::whereMonth('fecha', $mes)->whereYear('fecha', $ano)->count(),
            'citas_realizadas'  => Cita::whereMonth('fecha', $mes)->whereYear('fecha', $ano)->where('estado', 'realizada')->count(),
            'citas_canceladas'  => Cita::whereMonth('fecha', $mes)->whereYear('fecha', $ano)->whereIn('estado', ['cancelada','no_show'])->count(),
            'ventas_mes'        => (float) Venta::whereMonth('fecha', $mes)->whereYear('fecha', $ano)->where('estado', 'pagada')->sum('total'),
            'productos_total'   => Producto::where('activo', true)->count(),
            'productos_bajo'    => Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->where('activo', true)->count(),
        ];

        // Ranking productos con stock bajo
        $stockBajo = Producto::with('categoria')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('activo', true)
            ->orderBy('stock_actual')
            ->limit(20)->get();

        // Ventas por mes (últimos 6 meses)
        $ventasMes = collect();
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $total = Venta::whereYear('fecha', $fecha->year)->whereMonth('fecha', $fecha->month)
                ->where('estado', 'pagada')->sum('total');
            $ventasMes->push([
                'mes'   => $fecha->locale('es')->isoFormat('MMM YY'),
                'total' => (float) $total,
            ]);
        }

        return view('reportes.indicadores', compact('kpis', 'stockBajo', 'ventasMes'));
    }
}
