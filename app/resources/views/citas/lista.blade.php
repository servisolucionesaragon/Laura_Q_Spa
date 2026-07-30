@extends('layouts.app')
@section('titulo', 'Lista de citas')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-list-check text-spa-primary"></i> Lista de citas</h3>
            <small class="text-spa-muted">Vista cronológica de todas las citas.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('citas.index') }}" class="btn btn-spa-secondary"><i class="bi bi-calendar-week"></i> Calendario</a>
            <a href="{{ route('citas.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Nueva cita</a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-10">
            <select name="estado" class="form-select" onchange="this.form.submit()">
                <option value="">— Todos los estados —</option>
                @foreach(['pendiente','confirmada','realizada','cancelada','no_show'] as $e)
                    <option value="{{ $e }}" {{ $estado === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($citas->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-calendar-x" style="font-size:2.5rem;opacity:.4"></i><p>Sin citas.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Servicio</th><th>Profesional</th><th>Cabina</th><th>Estado</th><th>Total</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($citas as $c)
                    <tr>
                        <td>{{ $c->fecha->format('d/m/Y') }}</td>
                        <td><strong>{{ \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') }}</strong></td>
                        <td>{{ $c->cliente?->nombre_completo ?? '—' }}</td>
                        <td><small>{{ $c->servicios->pluck('descripcion')->join(', ') }}</small></td>
                        <td><small>{{ $c->profesional?->name ?? '—' }}</small></td>
                        <td><small>{{ $c->cabina?->nombre ?? '—' }}</small></td>
                        <td><span class="spa-badge {{ $c->estado_badge }}">{{ $c->estado_label }}</span></td>
                        <td>{{ $sim }} {{ number_format($c->total, 2) }}</td>
                        <td class="text-end" style="white-space:nowrap">
                            @if(in_array($c->estado, ['pendiente', 'confirmada']) && $c->fecha->isFuture() && $c->cliente?->numeroWhatsapp())
                                <a href="{{ $c->cliente->whatsappUrl($c->mensajeRecordatorio()) }}" target="_blank"
                                   class="btn btn-sm" style="background:#25D366;color:#fff" title="Recordatorio por WhatsApp"><i class="bi bi-whatsapp"></i></a>
                            @endif
                            <a href="{{ route('citas.show', $c) }}" class="btn btn-sm" style="background:var(--spa-info);color:#fff"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('citas.edit', $c) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $citas->links() }}</div>
    @endif
</div>
@endsection
