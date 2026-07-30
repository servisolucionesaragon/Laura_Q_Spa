@extends('layouts.app')
@section('titulo', 'Caja')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp
@include('layouts.partials.errors')

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-cash-stack text-spa-primary"></i> Caja</h3>
            <small class="text-spa-muted">Control diario de apertura, cierre, gastos e ingresos.</small>
        </div>
        @if(! $cajaAbierta)
            <a href="{{ route('caja.abrir') }}" class="btn btn-spa-primary"><i class="bi bi-unlock"></i> Abrir caja</a>
        @endif
    </div>

    @if($cajaAbierta)
        <div class="alert" style="background:#e0efe3;border-color:var(--spa-success);color:#2e6a3a">
            <i class="bi bi-unlock-fill"></i>
            <div>
                Hay una caja <strong>abierta</strong> desde el
                {{ $cajaAbierta->abierta_en->format('d/m/Y H:i') }}
                ({{ $cajaAbierta->usuario->name }}).
                <a href="{{ route('caja.show', $cajaAbierta) }}" class="alert-link">Ver caja abierta →</a>
            </div>
        </div>
    @else
        <div class="text-center py-4 text-spa-muted">
            <i class="bi bi-lock" style="font-size:2.5rem;opacity:.4"></i>
            <p class="mt-2">No hay ninguna caja abierta en este momento.</p>
        </div>
    @endif
</div>

<div class="spa-card">
    <div class="spa-card-header">
        <h4 style="margin:0"><i class="bi bi-clock-history text-spa-primary"></i> Historial de cajas cerradas</h4>
    </div>

    @if($historial->isEmpty())
        <div class="text-center py-4 text-spa-muted"><p>Aún no hay cajas cerradas.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Fecha</th><th>Responsable</th><th>Apertura</th><th>Cierre</th><th>Esperado</th><th>Diferencia</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($historial as $c)
                    <tr>
                        <td>{{ $c->fecha->format('d/m/Y') }}</td>
                        <td>{{ $c->usuario->name }}</td>
                        <td>{{ $sim }} {{ number_format($c->monto_apertura, 2) }}</td>
                        <td>{{ $sim }} {{ number_format($c->monto_cierre, 2) }}</td>
                        <td>{{ $sim }} {{ number_format($c->monto_esperado, 2) }}</td>
                        <td>
                            @php $dif = (float) $c->diferencia; @endphp
                            <span class="spa-badge {{ $dif == 0 ? 'success' : ($dif > 0 ? 'info' : 'danger') }}">
                                {{ $dif > 0 ? '+' : '' }}{{ $sim }} {{ number_format($dif, 2) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('caja.show', $c) }}" class="btn btn-sm" style="background:var(--spa-info);color:#fff"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $historial->links() }}</div>
    @endif
</div>
@endsection
