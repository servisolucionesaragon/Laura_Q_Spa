<?php

namespace App\Http\Controllers;

use App\Models\Cabina;
use App\Models\Cita;
use App\Models\CitaServicio;
use App\Models\Tratamiento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function index(Request $request): View
    {
        $vista = $request->get('vista', 'semana');

        return match ($vista) {
            'mes' => $this->vistaMes($request),
            'dia' => $this->vistaDia($request),
            default => $this->vistaSemana($request),
        };
    }

    protected function vistaSemana(Request $request): View
    {
        $fechaInicio = $request->filled('inicio')
            ? Carbon::parse($request->input('inicio'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $fechaFin = $fechaInicio->copy()->addDays(6);

        $citas = Cita::with(['cliente', 'profesional', 'cabina', 'servicios'])
            ->whereBetween('fecha', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy(fn ($c) => $c->fecha->format('Y-m-d'));

        $dias = collect();
        for ($i = 0; $i < 7; $i++) {
            $d = $fechaInicio->copy()->addDays($i);
            $dias->push([
                'fecha'   => $d->format('Y-m-d'),
                'dia'     => $d->locale('es')->isoFormat('dddd'),
                'numero'  => $d->format('d'),
                'mes'     => $d->locale('es')->isoFormat('MMM'),
                'es_hoy'  => $d->isToday(),
                'citas'   => $citas[$d->format('Y-m-d')] ?? collect(),
            ]);
        }

        return view('citas.index', [
            'vista' => 'semana',
            'dias' => $dias,
            'inicio' => $fechaInicio,
            'fin' => $fechaFin,
            'semanaAnterior' => $fechaInicio->copy()->subWeek()->format('Y-m-d'),
            'semanaSiguiente'=> $fechaInicio->copy()->addWeek()->format('Y-m-d'),
            'hoyInicio' => Carbon::now()->startOfWeek()->format('Y-m-d'),
        ]);
    }

    protected function vistaDia(Request $request): View
    {
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->input('fecha'))
            : Carbon::today();

        $citas = Cita::with(['cliente', 'profesional', 'cabina', 'servicios'])
            ->whereDate('fecha', $fecha->format('Y-m-d'))
            ->orderBy('hora_inicio')
            ->get();

        return view('citas.index', [
            'vista' => 'dia',
            'fechaDia' => $fecha,
            'citasDia' => $citas,
            'diaAnterior' => $fecha->copy()->subDay()->format('Y-m-d'),
            'diaSiguiente' => $fecha->copy()->addDay()->format('Y-m-d'),
            'hoyFecha' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    protected function vistaMes(Request $request): View
    {
        $mesRef = $request->filled('mes')
            ? Carbon::parse($request->input('mes') . '-01')
            : Carbon::now();
        $mesRef = $mesRef->startOfMonth();

        $inicioGrid = $mesRef->copy()->startOfWeek();
        $finGrid = $mesRef->copy()->endOfMonth()->endOfWeek();

        $citas = Cita::with(['cliente', 'profesional'])
            ->whereBetween('fecha', [$inicioGrid->format('Y-m-d'), $finGrid->format('Y-m-d')])
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy(fn ($c) => $c->fecha->format('Y-m-d'));

        $semanas = collect();
        $cursor = $inicioGrid->copy();
        while ($cursor->lte($finGrid)) {
            $semana = collect();
            for ($i = 0; $i < 7; $i++) {
                $semana->push([
                    'fecha'         => $cursor->format('Y-m-d'),
                    'numero'        => $cursor->format('j'),
                    'es_hoy'        => $cursor->isToday(),
                    'es_mes_actual' => $cursor->month === $mesRef->month,
                    'citas'         => $citas[$cursor->format('Y-m-d')] ?? collect(),
                ]);
                $cursor->addDay();
            }
            $semanas->push($semana);
        }

        return view('citas.index', [
            'vista' => 'mes',
            'mesRef' => $mesRef,
            'semanas' => $semanas,
            'mesAnterior' => $mesRef->copy()->subMonth()->format('Y-m'),
            'mesSiguiente' => $mesRef->copy()->addMonth()->format('Y-m'),
            'hoyMes' => Carbon::now()->format('Y-m'),
        ]);
    }

    public function lista(Request $request): View
    {
        $estado = $request->get('estado');
        $citas = Cita::with(['cliente', 'profesional', 'cabina', 'servicios'])
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha')->orderByDesc('hora_inicio')
            ->paginate(20)->withQueryString();
        return view('citas.lista', compact('citas', 'estado'));
    }

    public function create(Request $request): View
    {
        $cita = new Cita([
            'fecha' => $request->get('fecha', now()->format('Y-m-d')),
            'hora_inicio' => $request->get('hora', '10:00'),
            'estado' => 'pendiente',
        ]);
        return $this->vistaForm($cita);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos, $request) {
            $tratamiento = Tratamiento::find($datos['tratamiento_id']);
            $duracion = $tratamiento?->duracion_min ?? 30;
            $horaFin = Carbon::parse($datos['hora_inicio'])->addMinutes($duracion);

            $cita = Cita::create([
                'cliente_id'    => $datos['cliente_id'],
                'profesional_id'=> $datos['profesional_id'] ?? null,
                'cabina_id'     => $datos['cabina_id'] ?? null,
                'fecha'         => $datos['fecha'],
                'hora_inicio'   => $datos['hora_inicio'],
                'hora_fin'      => $horaFin->format('H:i:s'),
                'estado'        => $datos['estado'],
                'total'         => $tratamiento?->precio ?? 0,
                'notas'         => $datos['notas'] ?? null,
                'creado_por'    => auth()->id(),
            ]);

            CitaServicio::create([
                'cita_id'        => $cita->id,
                'tratamiento_id' => $datos['tratamiento_id'],
                'descripcion'    => $tratamiento?->nombre ?? 'Servicio',
                'duracion_min'   => $duracion,
                'precio'         => $tratamiento?->precio ?? 0,
            ]);
        });

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function show(Cita $cita): View
    {
        $cita->load(['cliente', 'profesional', 'cabina', 'servicios.tratamiento', 'creadaPor']);
        return view('citas.show', compact('cita'));
    }

    public function edit(Cita $cita): View
    {
        return $this->vistaForm($cita);
    }

    public function update(Request $request, Cita $cita): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos, $cita) {
            $tratamiento = Tratamiento::find($datos['tratamiento_id']);
            $duracion = $tratamiento?->duracion_min ?? 30;
            $horaFin = Carbon::parse($datos['hora_inicio'])->addMinutes($duracion);

            $cita->update([
                'cliente_id'    => $datos['cliente_id'],
                'profesional_id'=> $datos['profesional_id'] ?? null,
                'cabina_id'     => $datos['cabina_id'] ?? null,
                'fecha'         => $datos['fecha'],
                'hora_inicio'   => $datos['hora_inicio'],
                'hora_fin'      => $horaFin->format('H:i:s'),
                'estado'        => $datos['estado'],
                'total'         => $tratamiento?->precio ?? 0,
                'notas'         => $datos['notas'] ?? null,
            ]);

            $cita->servicios()->delete();
            CitaServicio::create([
                'cita_id'        => $cita->id,
                'tratamiento_id' => $datos['tratamiento_id'],
                'descripcion'    => $tratamiento?->nombre ?? 'Servicio',
                'duracion_min'   => $duracion,
                'precio'         => $tratamiento?->precio ?? 0,
            ]);
        });

        return redirect()->route('citas.show', $cita)->with('success', 'Cita actualizada.');
    }

    public function cambiarEstado(Request $request, Cita $cita): RedirectResponse
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmada,realizada,cancelada,no_show']);
        $cita->update(['estado' => $request->input('estado')]);
        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(Cita $cita): RedirectResponse
    {
        $cita->delete();
        return redirect()->route('citas.index')->with('success', 'Cita eliminada.');
    }

    protected function validar(Request $request): array
    {
        return $request->validate([
            'cliente_id'      => 'required|exists:clientes,id',
            'profesional_id'  => 'nullable|exists:users,id',
            'cabina_id'       => 'nullable|exists:cabinas,id',
            'tratamiento_id'  => 'required|exists:tratamientos,id',
            'fecha'           => 'required|date',
            'hora_inicio'     => 'required|date_format:H:i',
            'estado'          => 'required|in:pendiente,confirmada,realizada,cancelada,no_show',
            'notas'           => 'nullable|string',
        ]);
    }

    protected function vistaForm(Cita $cita): View
    {
        return view('citas.form', [
            'cita'          => $cita,
            'profesionales' => User::where('rol', 'profesional')->where('activo', true)->orderBy('name')->get(),
            'cabinas'       => Cabina::where('activo', true)->orderBy('nombre')->get(),
            'tratamientos'  => Tratamiento::with('categoria')->where('activo', true)->orderBy('nombre')->get(),
        ]);
    }
}
