<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaProductoController extends Controller
{
    public function index(): View
    {
        $categorias = CategoriaProducto::withCount('productos')->orderBy('nombre')->get();
        return view('productos.categorias', compact('categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate(['nombre' => 'required|string|max:100', 'activo' => 'nullable|boolean']);
        $datos['activo'] = $request->boolean('activo', true);
        CategoriaProducto::create($datos);
        return redirect()->route('categorias-productos.index')->with('success', 'Categoría creada.');
    }

    public function update(Request $request, CategoriaProducto $categoriaProducto): RedirectResponse
    {
        $datos = $request->validate(['nombre' => 'required|string|max:100', 'activo' => 'nullable|boolean']);
        $datos['activo'] = $request->boolean('activo');
        $categoriaProducto->update($datos);
        return redirect()->route('categorias-productos.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(CategoriaProducto $categoriaProducto): RedirectResponse
    {
        try {
            $categoriaProducto->delete();
            return redirect()->route('categorias-productos.index')->with('success', 'Categoría eliminada.');
        } catch (\Throwable $e) {
            return redirect()->route('categorias-productos.index')->with('error', 'No se puede eliminar.');
        }
    }
}
