<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $rol = $request->get('rol');

        $empleados = User::query()
            ->when($q, fn ($qb) => $qb->where(fn ($w) =>
                $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
            ->when($rol, fn ($qb) => $qb->where('rol', $rol))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('empleados.index', compact('empleados', 'q', 'rol'));
    }

    public function create(): View
    {
        return view('empleados.form', ['empleado' => new User(['rol' => 'profesional', 'activo' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);
        $datos['password'] = Hash::make($datos['password']);
        $datos['activo'] = $request->boolean('activo', true);
        User::create($datos);
        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente.');
    }

    public function edit(User $empleado): View
    {
        return view('empleados.form', compact('empleado'));
    }

    public function update(Request $request, User $empleado): RedirectResponse
    {
        $datos = $this->validar($request, $empleado->id);
        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }
        $datos['activo'] = $request->boolean('activo');
        $empleado->update($datos);
        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado.');
    }

    public function destroy(User $empleado): RedirectResponse
    {
        if ($empleado->id === auth()->id()) {
            return redirect()->route('empleados.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        try {
            $empleado->delete();
            return redirect()->route('empleados.index')->with('success', 'Empleado eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('empleados.index')->with('error', 'No se puede eliminar: tiene datos asociados.');
        }
    }

    protected function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => ['required', 'email', 'max:191', Rule::unique('users')->ignore($id)],
            'password' => $id ? 'nullable|string|min:6' : 'required|string|min:6',
            'rol'      => 'required|in:admin,recepcionista,profesional,cajero',
            'telefono' => 'nullable|string|max:30',
            'activo'   => 'nullable|boolean',
        ]);
    }
}
