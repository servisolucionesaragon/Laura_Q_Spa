<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $clientes = Cliente::buscar($q)
            ->withCount(['citas', 'ventas', 'bonos'])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();
        return view('clientes.index', compact('clientes', 'q'));
    }

    public function create(): View
    {
        return view('clientes.form', ['cliente' => new Cliente(['activo' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Cliente::create($this->validar($request));
        return redirect()->route('clientes.index')->with('success', 'Cliente registrado.');
    }

    public function show(Cliente $cliente): View
    {
        $cliente->load(['citas.servicios', 'citas.profesional', 'bonos.plantilla', 'ventas']);
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($this->validar($request));
        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        try {
            $cliente->delete();
            return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('clientes.index')->with('error', 'No se puede eliminar: tiene historial.');
        }
    }

    protected function validar(Request $request): array
    {
        return $request->validate([
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:191',
            'telefono'         => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'genero'           => 'nullable|in:F,M,O',
            'direccion'        => 'nullable|string|max:191',
            'ciudad'           => 'nullable|string|max:100',
            'documento'        => 'nullable|string|max:50',
            'alergias'         => 'nullable|string',
            'notas'            => 'nullable|string',
            'como_nos_conocio' => 'nullable|string|max:100',
            'acepta_marketing' => 'nullable|boolean',
            'activo'           => 'nullable|boolean',
        ]) + [
            'acepta_marketing' => $request->boolean('acepta_marketing'),
            'activo'           => $request->boolean('activo', true),
        ];
    }
}
