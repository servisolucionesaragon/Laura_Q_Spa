<?php

namespace App\Http\Controllers;

use App\Models\Bono;
use App\Models\BonoConsumo;
use App\Models\BonoPlantilla;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BonoController extends Controller
{
    public function index(Request $request): View
    {
        $estado = $request->get('estado');
        $bonos = Bono::with(['cliente', 'plantilla'])
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha_compra')
            ->paginate(20)->withQueryString();
        return view('bonos.index', compact('bonos', 'estado'));
    }

    public function create(): View
    {
        return view('bonos.form', [
            'bono'        => new Bono(['fecha_compra' => now()->format('Y-m-d'), 'estado' => 'activo']),
            'clientes'    => Cliente::where('activo', true)->orderBy('nombre')->get(),
            'plantillas'  => BonoPlantilla::activas()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'plantilla_id' => 'required|exists:bonos_plantillas,id',
            'fecha_compra' => 'required|date',
            'precio_pagado'=> 'required|numeric|min:0',
            'notas'        => 'nullable|string',
        ]);

        $plantilla = BonoPlantilla::findOrFail($datos['plantilla_id']);
        Bono::create([
            'codigo'             => 'BNO-' . strtoupper(Str::random(6)),
            'cliente_id'         => $datos['cliente_id'],
            'plantilla_id'       => $plantilla->id,
            'nombre'             => $plantilla->nombre,
            'sesiones_total'     => $plantilla->sesiones_total,
            'sesiones_usadas'    => 0,
            'fecha_compra'       => $datos['fecha_compra'],
            'fecha_vencimiento'  => Carbon::parse($datos['fecha_compra'])->addDays($plantilla->validez_dias),
            'precio_pagado'      => $datos['precio_pagado'],
            'estado'             => 'activo',
            'notas'              => $datos['notas'] ?? null,
        ]);

        return redirect()->route('bonos.index')->with('success', 'Bono vendido y registrado.');
    }

    public function show(Bono $bono): View
    {
        $bono->load(['cliente', 'plantilla', 'consumos.user', 'consumos.cita']);
        return view('bonos.show', compact('bono'));
    }

    public function consumir(Request $request, Bono $bono): RedirectResponse
    {
        if ($bono->sesiones_restantes <= 0) {
            return back()->with('error', 'Este bono ya no tiene sesiones disponibles.');
        }

        $datos = $request->validate([
            'descripcion' => 'nullable|string|max:191',
            'fecha'       => 'required|date',
        ]);

        BonoConsumo::create([
            'bono_id'     => $bono->id,
            'user_id'     => auth()->id(),
            'fecha'       => $datos['fecha'],
            'descripcion' => $datos['descripcion'] ?? 'Sesión consumida',
        ]);

        $bono->increment('sesiones_usadas');
        $bono->actualizarEstado();

        return back()->with('success', 'Sesión registrada correctamente.');
    }

    public function destroy(Bono $bono): RedirectResponse
    {
        $bono->delete();
        return redirect()->route('bonos.index')->with('success', 'Bono eliminado.');
    }
}
