@extends('layouts.app')
@section('titulo', 'Agenda')

@push('styles')
<style>
    .vista-switch { display: flex; gap: .25rem; }
    .vista-switch a {
        padding: .5rem 1rem;
        border: 1px solid var(--spa-border);
        background: var(--spa-surface);
        border-radius: var(--spa-radius-sm);
        color: var(--spa-muted);
        font-weight: 500;
        font-size: .88rem;
        text-decoration: none;
    }
    .vista-switch a.active { background: var(--spa-secondary); color: #fff; border-color: var(--spa-secondary); }

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

    /* ---- Vista mes ---- */
    .mes-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
    .mes-dow { text-align: center; font-size: .72rem; text-transform: uppercase; color: var(--spa-muted); font-weight: 700; padding-bottom: .35rem; letter-spacing: .5px; }
    .mes-day {
        background: var(--spa-surface);
        border: 1px solid var(--spa-border);
        border-radius: 8px;
        min-height: 92px;
        padding: .4rem;
        opacity: .45;
    }
    .mes-day.actual { opacity: 1; }
    .mes-day.hoy { border-color: var(--spa-primary-dark); box-shadow: 0 0 0 2px rgba(163, 88, 128, .15); }
    .mes-day .num { font-weight: 700; color: var(--spa-secondary); font-size: .85rem; }
    .mes-day a.num-link { text-decoration: none; }
    .mes-day .mini-cita { font-size: .68rem; padding: 1px 4px; border-radius: 4px; margin-top: 2px; background: var(--spa-surface-soft); border-left: 2px solid var(--spa-primary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; text-decoration: none; color: var(--spa-text); }
    .mes-day .mini-mas { font-size: .66rem; color: var(--spa-muted); margin-top: 2px; }

    /* ---- Vista día ---- */
    .dia-lista .cal-cita { font-size: .92rem; padding: .7rem .9rem; }
    .dia-lista .cal-cita .hora { font-size: .95rem; }
</style>
@endpush

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3>
                <i class="bi bi-calendar-week-fill text-spa-primary"></i>
                @if($vista === 'mes') Agenda · {{ $mesRef->locale('es')->isoFormat('MMMM YYYY') }}
                @elseif($vista === 'dia') Agenda · {{ $fechaDia->locale('es')->isoFormat('dddd D [de] MMMM') }}
                @else Agenda semanal
                @endif
            </h3>
            @if($vista === 'semana')
                <small class="text-spa-muted">{{ $inicio->locale('es')->isoFormat('D [de] MMM') }} — {{ $fin->locale('es')->isoFormat('D [de] MMM YYYY') }}</small>
            @endif
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('citas.lista') }}" class="btn btn-spa-secondary"><i class="bi bi-list"></i> Lista</a>
            <a href="{{ route('citas.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Nueva cita</a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="vista-switch">
            <a href="{{ route('citas.index', ['vista' => 'mes']) }}" class="{{ $vista === 'mes' ? 'active' : '' }}">Mes</a>
            <a href="{{ route('citas.index', ['vista' => 'semana']) }}" class="{{ $vista === 'semana' ? 'active' : '' }}">Semana</a>
            <a href="{{ route('citas.index', ['vista' => 'dia']) }}" class="{{ $vista === 'dia' ? 'active' : '' }}">Día</a>
        </div>

        <div class="d-flex gap-2">
            @if($vista === 'mes')
                <a href="{{ route('citas.index', ['vista' => 'mes', 'mes' => $mesAnterior]) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-chevron-left"></i></a>
                <a href="{{ route('citas.index', ['vista' => 'mes', 'mes' => $hoyMes]) }}" class="btn btn-spa-secondary btn-sm">Hoy</a>
                <a href="{{ route('citas.index', ['vista' => 'mes', 'mes' => $mesSiguiente]) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-chevron-right"></i></a>
            @elseif($vista === 'dia')
                <a href="{{ route('citas.index', ['vista' => 'dia', 'fecha' => $diaAnterior]) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-chevron-left"></i></a>
                <a href="{{ route('citas.index', ['vista' => 'dia', 'fecha' => $hoyFecha]) }}" class="btn btn-spa-secondary btn-sm">Hoy</a>
                <a href="{{ route('citas.index', ['vista' => 'dia', 'fecha' => $diaSiguiente]) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-chevron-right"></i></a>
            @else
                <a href="{{ route('citas.index', ['vista' => 'semana', 'inicio' => $semanaAnterior]) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-chevron-left"></i> Anterior</a>
                <a href="{{ route('citas.index', ['vista' => 'semana', 'inicio' => $hoyInicio]) }}" class="btn btn-spa-secondary btn-sm">Hoy</a>
                <a href="{{ route('citas.index', ['vista' => 'semana', 'inicio' => $semanaSiguiente]) }}" class="btn btn-spa-secondary btn-sm">Siguiente <i class="bi bi-chevron-right"></i></a>
            @endif
        </div>

        <div style="font-size:.85rem;color:var(--spa-muted)">
            <span class="spa-badge warning">Pendiente</span>
            <span class="spa-badge info">Confirmada</span>
            <span class="spa-badge success">Realizada</span>
            <span class="spa-badge danger">Cancelada</span>
        </div>
    </div>

    {{-- ===================== VISTA SEMANA ===================== --}}
    @if($vista === 'semana')
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
    @endif

    {{-- ===================== VISTA MES ===================== --}}
    @if($vista === 'mes')
        <div class="mes-grid mb-1">
            @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $dow)
                <div class="mes-dow">{{ $dow }}</div>
            @endforeach
        </div>
        @foreach($semanas as $semana)
            <div class="mes-grid mb-1">
                @foreach($semana as $d)
                    <div class="mes-day {{ $d['es_mes_actual'] ? 'actual' : '' }} {{ $d['es_hoy'] ? 'hoy' : '' }}">
                        <a href="{{ route('citas.index', ['vista' => 'dia', 'fecha' => $d['fecha']]) }}" class="num-link">
                            <span class="num">{{ $d['numero'] }}</span>
                        </a>
                        @foreach($d['citas']->take(3) as $c)
                            <a href="{{ route('citas.show', $c) }}" class="mini-cita {{ $c->estado }}" title="{{ \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') }} {{ $c->cliente?->nombre_completo }}">
                                {{ \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') }} {{ $c->cliente?->nombre_completo }}
                            </a>
                        @endforeach
                        @if($d['citas']->count() > 3)
                            <a href="{{ route('citas.index', ['vista' => 'dia', 'fecha' => $d['fecha']]) }}" class="mini-mas">+{{ $d['citas']->count() - 3 }} más</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif

    {{-- ===================== VISTA DÍA ===================== --}}
    @if($vista === 'dia')
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('citas.create', ['fecha' => $fechaDia->format('Y-m-d')]) }}" class="btn btn-spa-secondary btn-sm">
                <i class="bi bi-plus-lg"></i> Nueva cita este día
            </a>
        </div>
        <div class="dia-lista">
            @if($citasDia->isEmpty())
                <div class="text-center py-4 text-spa-muted"><i class="bi bi-calendar-x" style="font-size:2.5rem;opacity:.4"></i><p class="mt-2">Sin citas este día.</p></div>
            @else
                @foreach($citasDia as $c)
                    <a href="{{ route('citas.show', $c) }}" class="cal-cita {{ $c->estado }}">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="hora">{{ \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') }} — {{ \Carbon\Carbon::parse($c->hora_fin)->format('H:i') }}</span>
                                <strong class="ms-2">{{ $c->cliente?->nombre_completo ?? '—' }}</strong>
                                <div style="font-size:.8rem;color:var(--spa-muted);margin-top:2px">
                                    {{ $c->servicios->pluck('descripcion')->join(', ') }}
                                    @if($c->profesional) · {{ $c->profesional->name }}@endif
                                    @if($c->cabina) · {{ $c->cabina->nombre }}@endif
                                </div>
                            </div>
                            <span class="spa-badge {{ $c->estado_badge }}">{{ $c->estado_label }}</span>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    @endif
</div>
@endsection
