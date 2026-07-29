<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $proveedores = Proveedor::withCount('productos')
            ->when($q, fn ($qb) => $qb->where(fn ($w) =>
                $w->where('nombre', 'like', "%{$q}%")
                  ->orWhere('contacto', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")))
            ->orderBy('nombre')->paginate(15)->withQueryString();
        return view('proveedores.index', compact('proveedores', 'q'));
    }

    public function create(): View
    {
        return view('proveedores.form', ['proveedor' => new Proveedor(['activo' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Proveedor::create($this->validar($request));
        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.form', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($this->validar($request));
        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        try {
            $proveedor->delete();
            return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('proveedores.index')->with('error', 'No se puede eliminar: tiene productos asociados.');
        }
    }

    protected function validar(Request $request): array
    {
        $datos = $request->validate([
            'nombre'    => 'required|string|max:191',
            'contacto'  => 'nullable|string|max:191',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:191',
            'direccion' => 'nullable|string|max:191',
            'notas'     => 'nullable|string',
            'activo'    => 'nullable|boolean',
        ]);
        $datos['activo'] = $request->boolean('activo', true);
        return $datos;
    }
}
