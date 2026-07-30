<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetodoPagoController extends Controller
{
    public function index(): View
    {
        $metodosPago = MetodoPago::orderBy('nombre')->get();
        return view('ventas.metodos-pago', compact('metodosPago'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre',
            'activo' => 'nullable|boolean',
        ]);
        $datos['activo'] = $request->boolean('activo', true);
        MetodoPago::create($datos);
        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago creado.');
    }

    public function update(Request $request, MetodoPago $metodoPago): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre,' . $metodoPago->id,
            'activo' => 'nullable|boolean',
        ]);
        $datos['activo'] = $request->boolean('activo');
        $metodoPago->update($datos);
        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago actualizado.');
    }

    public function destroy(MetodoPago $metodoPago): RedirectResponse
    {
        try {
            $metodoPago->delete();
            return redirect()->route('metodos-pago.index')->with('success', 'Método de pago eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('metodos-pago.index')->with('error', 'No se puede eliminar.');
        }
    }
}
