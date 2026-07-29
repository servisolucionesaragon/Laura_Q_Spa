<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTratamiento;
use App\Models\Tratamiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TratamientoController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $categoriaId = $request->get('categoria_id');

        $tratamientos = Tratamiento::with('categoria')
            ->when($q, fn ($qb) => $qb->where('nombre', 'like', "%{$q}%"))
            ->when($categoriaId, fn ($qb) => $qb->where('categoria_id', $categoriaId))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $categorias = CategoriaTratamiento::activas()->orderBy('nombre')->get();

        return view('tratamientos.index', compact('tratamientos', 'categorias', 'q', 'categoriaId'));
    }

    public function create(): View
    {
        $categorias = CategoriaTratamiento::activas()->orderBy('nombre')->get();
        return view('tratamientos.form', [
            'tratamiento' => new Tratamiento(['duracion_min' => 30, 'precio' => 0, 'comision_porcentaje' => 0, 'requiere_cabina' => true, 'activo' => true]),
            'categorias'  => $categorias,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Tratamiento::create($this->validar($request));
        return redirect()->route('tratamientos.index')->with('success', 'Tratamiento creado.');
    }

    public function edit(Tratamiento $tratamiento): View
    {
        $categorias = CategoriaTratamiento::orderBy('nombre')->get();
        return view('tratamientos.form', compact('tratamiento', 'categorias'));
    }

    public function update(Request $request, Tratamiento $tratamiento): RedirectResponse
    {
        $tratamiento->update($this->validar($request));
        return redirect()->route('tratamientos.index')->with('success', 'Tratamiento actualizado.');
    }

    public function destroy(Tratamiento $tratamiento): RedirectResponse
    {
        try {
            $tratamiento->delete();
            return redirect()->route('tratamientos.index')->with('success', 'Tratamiento eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('tratamientos.index')->with('error', 'No se puede eliminar: tiene citas asociadas.');
        }
    }

    protected function validar(Request $request): array
    {
        $datos = $request->validate([
            'categoria_id'        => 'nullable|exists:categorias_tratamientos,id',
            'nombre'              => 'required|string|max:191',
            'descripcion'         => 'nullable|string',
            'duracion_min'        => 'required|integer|min:5|max:480',
            'precio'              => 'required|numeric|min:0',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'requiere_cabina'     => 'nullable|boolean',
            'activo'              => 'nullable|boolean',
        ]);
        $datos['requiere_cabina'] = $request->boolean('requiere_cabina');
        $datos['activo'] = $request->boolean('activo', true);
        $datos['comision_porcentaje'] = $datos['comision_porcentaje'] ?? 0;
        return $datos;
    }
}
