@extends('layouts.app')
@section('titulo', 'Agenda · Calendario semanal')

@push('styles')
<style>
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: .5rem; }
    @media (max-width: 992px) { .cal-grid { grid-template-columns: 1fr; } }
    .cal-day {
        background: var(--spa-surface);
        border: 1px solid var(--spa-border);
        border-radius: var(--spa-radius-sm);
        padding: .85rem .7rem;
        min-height: 200px;
    }
    .cal-day.hoy {
        background: linear-gradient(180deg, var(--spa-surface-tint) 0%, var(--spa-surface) 100%);
        border-color: var(--spa-primary-dark);
        box-shadow: 0 0 0 3px rgba(163, 88, 128, .15);
    }
    .cal-day .head {
        display: flex; align-items: center; justify-content: space-between;
        padding-bottom: .5rem; margin-bottom: .5rem;
        border-bottom: 1px solid var(--spa-border-soft);
    }
    .cal-day .head .dia { font-size: .72rem; text-transform: uppercase; color: var(--spa-muted); letter-spacing: .8px; font-weight: 600; }
    .cal-day .head .num { font-size: 1.4rem; font-weight: 700; color: var(--spa-secondary); line-height: 1; }
    .cal-day.hoy .head .num { color: var(--spa-primary-dark); }
    .cal-cita {
        display: block;
        background: var(--spa-surface-soft);
        border-left: 3px solid var(--spa-primary);
        border-radius: 6px;
        padding: .4rem .55rem;
        margin-bottom: .35rem;
        text-decoration: none;
        color: var(--spa-text);
        font-size: .82rem;
        line-height: 1.3;
        transition: all .15s ease;
    }
    .cal-cita:hover { transform: translateX(2px); box-shadow: var(--spa-shadow-soft); color: var(--spa-secondary); }
    .cal-cita .hora { font-weight: 700; color: var(--spa-primary-dark); font-size: .82rem; }
    .cal-cita.pendiente  { border-left-color: #c47736; }
    .cal-cita.confirmada { border-left-color: #487da0; }
    .cal-cita.realizada  { border-left-color: #4d8b58; }
    .cal-cita.cancelada  { border-left-color: #b04848; opacity: .65; }
    .cal-cita.no_show    { border-left-color: #7a3d5e; opacity: .65; }
</style>
@endpush

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-calendar-week-fill text-spa-primary"></i> Agenda semanal</h3>
            <small class="text-spa-muted">{{ $inicio->locale('es')->isoFormat('D [de] MMM') }} — {{ $fin->locale('es')->isoFormat('D [de] MMM YYYY') }}</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('citas.lista') }}" class="btn btn-spa-secondary"><i class="bi bi-list"></i> Lista</a>
            <a href="{{ route('citas.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Nueva cita</a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex gap-2">
            <a href="{{ route('citas.index', ['inicio' => $semanaAnterior]) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-chevron-left"></i> Anterior</a>
            <a href="{{ route('citas.index', ['inicio' => $hoyInicio]) }}" class="btn btn-spa-secondary btn-sm">Hoy</a>
            <a href="{{ route('citas.index', ['inicio' => $semanaSiguiente]) }}" class="btn btn-spa-secondary btn-sm">Siguiente <i class="bi bi-chevron-right"></i></a>
        </div>
        <div style="font-size:.85rem;color:var(--spa-muted)">
            <span class="spa-badge warning">Pendiente</span>
            <span class="spa-badge info">Confirmada</span>
            <span class="spa-badge success">Realizada</span>
            <span class="spa-badge danger">Cancelada</span>
        </div>
    </div>

    <div class="cal-grid">
        @foreach($dias as $d)
            <div class="cal-day {{ $d['es_hoy'] ? 'hoy' : '' }}">
                <div class="head">
                    <div>
                        <div class="dia">{{ $d['dia'] }}</div>
                        <div class="num">{{ $d['numero'] }} <span style="font-size:.75rem;color:var(--spa-muted);font-weight:500">{{ $d['mes'] }}</span></div>
                    </div>
                    <a href="{{ route('citas.create', ['fecha' => $d['fecha']]) }}" class="btn btn-spa-secondary btn-sm" title="Nueva cita en {{ $d['fecha'] }}">
                        <i class="bi bi-plus"></i>
                    </a>
                </div>

                @if($d['citas']->isEmpty())
                    <div style="text-align:center;color:var(--spa-muted);font-size:.78rem;padding:1.5rem 0;opacity:.5">Sin citas</div>
                @else
                    @foreach($d['citas'] as $c)
                        <a href="{{ route('citas.show', $c) }}" class="cal-cita {{ $c->estado }}">
                            <span class="hora">{{ \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') }}</span>
                            <strong>{{ $c->cliente?->nombre_completo ?? '—' }}</strong>
                            <div style="font-size:.74rem;color:var(--spa-muted);margin-top:1px">
                                {{ \Illuminate\Support\Str::limit($c->servicios->pluck('descripcion')->join(', '), 28) }}
                                @if($c->profesional) · {{ explode(' ', $c->profesional->name)[0] }}@endif
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
