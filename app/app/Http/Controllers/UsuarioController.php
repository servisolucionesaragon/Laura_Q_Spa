<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::orderBy('name')->paginate(20);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        return view('usuarios.form', ['usuario' => new User(['rol' => 'admin', 'activo' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);
        $datos['password'] = Hash::make($datos['password']);
        $datos['activo'] = $request->boolean('activo', true);
        User::create($datos);
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.form', ['usuario' => $usuario]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $this->validar($request, $usuario->id);
        if (! empty($datos['password'])) $datos['password'] = Hash::make($datos['password']);
        else unset($datos['password']);
        $datos['activo'] = $request->boolean('activo');
        $usuario->update($datos);
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
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
