<?php

namespace App\Http\Controllers;

use App\Models\Cabina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CabinaController extends Controller
{
    public function index(): View
    {
        $cabinas = Cabina::orderBy('nombre')->get();
        return view('cabinas.index', compact('cabinas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:10',
            'activo'      => 'nullable|boolean',
        ]);
        $datos['activo'] = $request->boolean('activo', true);
        Cabina::create($datos);
        return redirect()->route('cabinas.index')->with('success', 'Cabina creada correctamente.');
    }

    public function update(Request $request, Cabina $cabina): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:10',
            'activo'      => 'nullable|boolean',
        ]);
        $datos['activo'] = $request->boolean('activo');
        $cabina->update($datos);
        return redirect()->route('cabinas.index')->with('success', 'Cabina actualizada.');
    }

    public function destroy(Cabina $cabina): RedirectResponse
    {
        try {
            $cabina->delete();
            return redirect()->route('cabinas.index')->with('success', 'Cabina eliminada.');
        } catch (\Throwable $e) {
            return redirect()->route('cabinas.index')->with('error', 'No se puede eliminar: tiene citas asociadas.');
        }
    }
}
