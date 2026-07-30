@extends('layouts.app')
@section('titulo', 'Cita #' . $cita->id)

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-calendar-event text-spa-primary"></i> Cita del {{ $cita->fecha->locale('es')->isoFormat('D [de] MMMM YYYY') }}</h3>
            <small class="text-spa-muted">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} — {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($cita->cliente?->numeroWhatsapp())
                <a href="{{ $cita->cliente->whatsappUrl($cita->mensajeRecordatorio()) }}" target="_blank"
                   class="btn" style="background:#25D366;color:#fff"><i class="bi bi-whatsapp"></i> Recordatorio</a>
            @endif
            <a href="{{ route('citas.edit', $cita) }}" class="btn btn-spa-primary"><i class="bi bi-pencil"></i> Editar</a>
            <a href="{{ route('citas.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <h5 style="color:var(--spa-secondary)">Cliente</h5>
            <p>
                <strong style="font-size:1.05rem">{{ $cita->cliente?->nombre_completo }}</strong><br>
                @if($cita->cliente?->telefono)<i class="bi bi-telephone"></i> {{ $cita->cliente->telefono }}<br>@endif
                @if($cita->cliente?->email)<small class="text-spa-muted"><i class="bi bi-envelope"></i> {{ $cita->cliente->email }}</small>@endif
            </p>
            <a href="{{ route('clientes.show', $cita->cliente) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-person"></i> Ver ficha completa</a>
        </div>

        <div class="col-md-6">
            <h5 style="color:var(--spa-secondary)">Detalles</h5>
            <table class="table" style="font-size:.92rem">
                <tr><td>Profesional</td><td><strong>{{ $cita->profesional?->name ?? '—' }}</strong></td></tr>
                <tr><td>Cabina</td><td>{{ $cita->cabina?->nombre ?? '—' }}</td></tr>
                <tr><td>Estado</td><td><span class="spa-badge {{ $cita->estado_badge }}">{{ $cita->estado_label }}</span></td></tr>
                <tr><td>Total</td><td><strong>{{ $sim }} {{ number_format($cita->total, 2) }}</strong></td></tr>
                <tr><td>Creada por</td><td><small>{{ $cita->creadaPor?->name ?? '—' }} · {{ $cita->created_at->format('d/m/Y H:i') }}</small></td></tr>
            </table>
        </div>

        <div class="col-12">
            <h5 style="color:var(--spa-secondary)">Servicios</h5>
            <table class="spa-table">
                <thead><tr><th>Servicio</th><th>Duración</th><th>Precio</th></tr></thead>
                <tbody>
                @foreach($cita->servicios as $s)
                    <tr><td>{{ $s->descripcion }}</td><td>{{ $s->duracion_min }} min</td><td>{{ $sim }} {{ number_format($s->precio, 2) }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($cita->notas)
        <div class="col-12">
            <div class="alert alert-info"><i class="bi bi-journal-text"></i><div><strong>Notas</strong><br>{{ $cita->notas }}</div></div>
        </div>
        @endif
    </div>

    <hr style="border-color:var(--spa-border-soft)">

    {{-- Cambio rápido de estado --}}
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <span style="font-weight:600;color:var(--spa-secondary);align-self:center">Cambiar estado:</span>
        @foreach(['pendiente'=>'warning','confirmada'=>'info','realizada'=>'success','cancelada'=>'danger','no_show'=>'danger'] as $est=>$col)
            @if($cita->estado !== $est)
                <div class="d-flex" style="gap:2px">
                    <form action="{{ route('citas.estado', $cita) }}" method="POST" class="d-inline">
                        @csrf <input type="hidden" name="estado" value="{{ $est }}">
                        <button class="btn btn-sm" style="background:var(--spa-{{ $col }});color:#fff;{{ $cita->cliente?->numeroWhatsapp() ? 'border-top-right-radius:0;border-bottom-right-radius:0' : '' }}">
                            {{ ucfirst(str_replace('_',' ',$est)) }}
                        </button>
                    </form>
                    @if($cita->cliente?->numeroWhatsapp())
                        <a href="{{ $cita->cliente->whatsappUrl($cita->mensajeCambioEstado($est)) }}" target="_blank"
                           class="btn btn-sm" style="background:#25D366;color:#fff;border-top-left-radius:0;border-bottom-left-radius:0"
                           title="Avisar por WhatsApp que la cita queda &quot;{{ $est }}&quot;">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    @endif
                </div>
            @endif
        @endforeach
        <form action="{{ route('citas.destroy', $cita) }}" method="POST" class="ms-auto" onsubmit="return confirm('¿Eliminar esta cita?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i> Eliminar</button>
        </form>
    </div>
    <div class="form-text mt-2">
        <i class="bi bi-info-circle"></i> El botón <i class="bi bi-whatsapp"></i> junto a cada estado solo avisa
        por WhatsApp — el estado real de la cita cambia al presionar el botón de color.
    </div>
</div>
@endsection
