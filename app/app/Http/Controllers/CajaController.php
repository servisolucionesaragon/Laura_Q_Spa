<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CajaController extends Controller
{
    public function index(): View
    {
        $cajaAbierta = Caja::abiertaActual();

        $historial = Caja::with('usuario')
            ->where('estado', 'cerrada')
            ->orderByDesc('cerrada_en')
            ->paginate(15);

        return view('caja.index', compact('cajaAbierta', 'historial'));
    }

    public function formAbrir(): View|RedirectResponse
    {
        if (Caja::abiertaActual()) {
            return redirect()->route('caja.index')->with('error', 'Ya hay una caja abierta.');
        }
        return view('caja.abrir');
    }

    public function abrir(Request $request): RedirectResponse
    {
        if (Caja::abiertaActual()) {
            return redirect()->route('caja.index')->with('error', 'Ya hay una caja abierta.');
        }

        $datos = $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'notas_apertura' => 'nullable|string|max:500',
        ]);

        $caja = Caja::create([
            'user_id'        => auth()->id(),
            'fecha'          => now()->toDateString(),
            'monto_apertura' => $datos['monto_apertura'],
            'notas_apertura' => $datos['notas_apertura'] ?? null,
            'estado'         => 'abierta',
            'abierta_en'     => now(),
        ]);

        return redirect()->route('caja.show', $caja)->with('success', 'Caja abierta correctamente.');
    }

    public function show(Caja $caja): View
    {
        $caja->load(['movimientos.usuario', 'usuario']);

        $totales = [
            'ventas_efectivo' => $caja->totalVentasEfectivo(),
            'ingresos'        => $caja->totalIngresos(),
            'egresos'         => $caja->totalEgresos(),
            'esperado'        => $caja->montoEsperadoCalculado(),
        ];

        return view('caja.show', compact('caja', 'totales'));
    }

    public function agregarMovimiento(Request $request, Caja $caja): RedirectResponse
    {
        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'Esta caja ya está cerrada.');
        }

        $datos = $request->validate([
            'tipo'     => 'required|in:ingreso,egreso',
            'concepto' => 'required|string|max:191',
            'monto'    => 'required|numeric|min:0.01',
        ]);

        MovimientoCaja::create($datos + ['caja_id' => $caja->id, 'user_id' => auth()->id()]);

        return back()->with('success', ucfirst($datos['tipo']) . ' registrado.');
    }

    public function eliminarMovimiento(Caja $caja, MovimientoCaja $movimiento): RedirectResponse
    {
        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'Esta caja ya está cerrada.');
        }
        if ($movimiento->caja_id !== $caja->id) {
            abort(404);
        }
        $movimiento->delete();
        return back()->with('success', 'Movimiento eliminado.');
    }

    public function cerrar(Request $request, Caja $caja): RedirectResponse
    {
        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'Esta caja ya está cerrada.');
        }

        $datos = $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'notas_cierre' => 'nullable|string|max:500',
        ]);

        $esperado = $caja->montoEsperadoCalculado();

        $caja->update([
            'monto_cierre'   => $datos['monto_cierre'],
            'monto_esperado' => $esperado,
            'diferencia'     => round($datos['monto_cierre'] - $esperado, 2),
            'notas_cierre'   => $datos['notas_cierre'] ?? null,
            'estado'         => 'cerrada',
            'cerrada_en'     => now(),
        ]);

        return redirect()->route('caja.show', $caja)->with('success', 'Caja cerrada correctamente.');
    }
}
