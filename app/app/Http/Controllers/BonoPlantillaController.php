<?php

namespace App\Http\Controllers;

use App\Models\BonoPlantilla;
use App\Models\Tratamiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BonoPlantillaController extends Controller
{
    public function index(): View
    {
        $plantillas = BonoPlantilla::with('tratamiento')->withCount('bonos')->orderBy('nombre')->get();
        return view('bonos.plantillas', compact('plantillas'));
    }

    public function create(): View
    {
        return view('bonos.plantilla-form', [
            'plantilla'    => new BonoPlantilla(['sesiones_total' => 5, 'validez_dias' => 180, 'activo' => true]),
            'tratamientos' => Tratamiento::activos()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BonoPlantilla::create($this->validar($request));
        return redirect()->route('bonos-plantillas.index')->with('success', 'Plantilla creada.');
    }

    public function edit(BonoPlantilla $bonoPlantilla): View
    {
        return view('bonos.plantilla-form', [
            'plantilla'    => $bonoPlantilla,
            'tratamientos' => Tratamiento::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, BonoPlantilla $bonoPlantilla): RedirectResponse
    {
        $bonoPlantilla->update($this->validar($request));
        return redirect()->route('bonos-plantillas.index')->with('success', 'Plantilla actualizada.');
    }

    public function destroy(BonoPlantilla $bonoPlantilla): RedirectResponse
    {
        try {
            $bonoPlantilla->delete();
            return redirect()->route('bonos-plantillas.index')->with('success', 'Plantilla eliminada.');
        } catch (\Throwable $e) {
            return redirect()->route('bonos-plantillas.index')->with('error', 'No se puede eliminar: tiene bonos vendidos.');
        }
    }

    protected function validar(Request $request): array
    {
        return $request->validate([
            'nombre'         => 'required|string|max:191',
            'descripcion'    => 'nullable|string',
            'precio'         => 'required|numeric|min:0',
            'sesiones_total' => 'required|integer|min:1',
            'validez_dias'   => 'required|integer|min:1',
            'tratamiento_id' => 'nullable|exists:tratamientos,id',
            'activo'         => 'nullable|boolean',
        ]) + ['activo' => $request->boolean('activo', true)];
    }
}
