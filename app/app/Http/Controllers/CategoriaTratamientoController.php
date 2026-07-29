<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTratamiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaTratamientoController extends Controller
{
    public function index(): View
    {
        $categorias = CategoriaTratamiento::withCount('tratamientos')->orderBy('nombre')->get();
        return view('tratamientos.categorias', compact('categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:10',
            'icono'       => 'nullable|string|max:50',
            'activo'      => 'nullable|boolean',
        ]);
        $datos['activo'] = $request->boolean('activo', true);
        CategoriaTratamiento::create($datos);
        return redirect()->route('categorias-tratamientos.index')->with('success', 'Categoría creada.');
    }

    public function update(Request $request, CategoriaTratamiento $categoriaTratamiento): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:10',
            'icono'       => 'nullable|string|max:50',
            'activo'      => 'nullable|boolean',
        ]);
        $datos['activo'] = $request->boolean('activo');
        $categoriaTratamiento->update($datos);
        return redirect()->route('categorias-tratamientos.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(CategoriaTratamiento $categoriaTratamiento): RedirectResponse
    {
        try {
            $categoriaTratamiento->delete();
            return redirect()->route('categorias-tratamientos.index')->with('success', 'Categoría eliminada.');
        } catch (\Throwable $e) {
            return redirect()->route('categorias-tratamientos.index')->with('error', 'No se puede eliminar.');
        }
    }
}
