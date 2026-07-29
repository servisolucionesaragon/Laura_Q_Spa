<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $categoria = $request->get('categoria_id');
        $stockBajo = $request->boolean('stock_bajo');

        $productos = Producto::with(['categoria', 'proveedor'])
            ->when($q, fn ($qb) => $qb->where(fn ($w) =>
                $w->where('nombre', 'like', "%{$q}%")->orWhere('codigo', 'like', "%{$q}%")))
            ->when($categoria, fn ($qb) => $qb->where('categoria_id', $categoria))
            ->when($stockBajo, fn ($qb) => $qb->whereColumn('stock_actual', '<=', 'stock_minimo'))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        $categorias = CategoriaProducto::orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'q', 'categoria', 'stockBajo'));
    }

    public function create(): View
    {
        return view('productos.form', [
            'producto'   => new Producto(['stock_actual' => 0, 'stock_minimo' => 0, 'unidad' => 'unidad', 'para_venta' => true, 'activo' => true]),
            'categorias' => CategoriaProducto::orderBy('nombre')->get(),
            'proveedores'=> Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);
        $producto = Producto::create($datos);

        if ($producto->stock_actual > 0) {
            MovimientoStock::create([
                'producto_id'    => $producto->id,
                'tipo'           => 'entrada',
                'cantidad'       => $producto->stock_actual,
                'stock_anterior' => 0,
                'stock_nuevo'    => $producto->stock_actual,
                'motivo'         => 'Stock inicial',
                'user_id'        => auth()->id(),
            ]);
        }
        return redirect()->route('productos.index')->with('success', 'Producto creado.');
    }

    public function edit(Producto $producto): View
    {
        return view('productos.form', [
            'producto'   => $producto,
            'categorias' => CategoriaProducto::orderBy('nombre')->get(),
            'proveedores'=> Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $datos = $this->validar($request, $producto->id);
        // No permitir modificar stock desde aquí (sólo desde movimientos)
        unset($datos['stock_actual']);
        $producto->update($datos);
        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        try {
            $producto->delete();
            return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('productos.index')->with('error', 'No se puede eliminar: tiene movimientos asociados.');
        }
    }

    public function moverStock(Request $request, Producto $producto): RedirectResponse
    {
        $datos = $request->validate([
            'tipo'     => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo'   => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($datos, $producto) {
            $anterior = $producto->stock_actual;
            $nuevo = match ($datos['tipo']) {
                'entrada' => $anterior + $datos['cantidad'],
                'salida'  => max(0, $anterior - $datos['cantidad']),
                'ajuste'  => $datos['cantidad'],
            };
            $producto->update(['stock_actual' => $nuevo]);
            MovimientoStock::create([
                'producto_id'    => $producto->id,
                'tipo'           => $datos['tipo'],
                'cantidad'       => $datos['cantidad'],
                'stock_anterior' => $anterior,
                'stock_nuevo'    => $nuevo,
                'motivo'         => $datos['motivo'] ?? '',
                'user_id'        => auth()->id(),
            ]);
        });

        return redirect()->route('productos.index')->with('success', 'Movimiento de stock registrado.');
    }

    public function movimientos(Producto $producto): View
    {
        $movimientos = $producto->movimientos()->with('user')->paginate(20);
        return view('productos.movimientos', compact('producto', 'movimientos'));
    }

    protected function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'categoria_id'    => 'nullable|exists:categorias_productos,id',
            'proveedor_id'    => 'nullable|exists:proveedores,id',
            'codigo'          => ['nullable', 'string', 'max:50', Rule::unique('productos')->ignore($id)],
            'nombre'          => 'required|string|max:191',
            'descripcion'     => 'nullable|string',
            'precio_compra'   => 'required|numeric|min:0',
            'precio_venta'    => 'required|numeric|min:0',
            'stock_actual'    => 'nullable|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'unidad'          => 'nullable|string|max:20',
            'para_venta'      => 'nullable|boolean',
            'para_uso_interno'=> 'nullable|boolean',
            'activo'          => 'nullable|boolean',
        ]) + [
            'para_venta'       => $request->boolean('para_venta'),
            'para_uso_interno' => $request->boolean('para_uso_interno'),
            'activo'           => $request->boolean('activo', true),
        ];
    }
}
