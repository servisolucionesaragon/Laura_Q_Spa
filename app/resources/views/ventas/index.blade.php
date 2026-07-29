@extends('layouts.app')
@section('titulo', 'Historial de ventas')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="spa-stat success">
            <div class="icon"><i class="bi bi-cash-coin"></i></div>
            <div class="label">Total facturado</div>
            <div class="value">{{ $sim }} {{ number_format($totales['total'], 2) }}</div>
            <div class="meta">{{ $desde }} → {{ $hasta }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="spa-stat info">
            <div class="icon"><i class="bi bi-receipt"></i></div>
            <div class="label">Ventas pagadas</div>
            <div class="value">{{ $totales['count'] }}</div>
            <div class="meta">en el período</div>
        </div>
    </div>
</div>

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-receipt text-spa-primary"></i> Historial de ventas</h3>
            <small class="text-spa-muted">Tickets emitidos en el período seleccionado.</small>
        </div>
        <a href="{{ route('ventas.tpv') }}" class="btn btn-spa-primary"><i class="bi bi-cash-stack"></i> Ir al TPV</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4"><label class="form-label">Desde</label><input type="date" name="desde" class="form-control" value="{{ $desde }}"></div>
        <div class="col-md-4"><label class="form-label">Hasta</label><input type="date" name="hasta" class="form-control" value="{{ $hasta }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-spa-secondary btn-block"><i class="bi bi-search"></i> Filtrar</button></div>
    </form>

    @if($ventas->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-receipt" style="font-size:2.5rem;opacity:.4"></i><p>Sin ventas en este período.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Número</th><th>Fecha</th><th>Cliente</th><th>Cajero</th><th>Método</th><th>Total</th><th>Estado</th><th class="text-end">Ver</th></tr></thead>
                <tbody>
                @foreach($ventas as $v)
                    <tr>
                        <td><strong><code>{{ $v->numero }}</code></strong></td>
                        <td>{{ $v->fecha->format('d/m/Y H:i') }}</td>
                        <td>{{ $v->cliente?->nombre_completo ?? '— Cliente eventual —' }}</td>
                        <td><small>{{ $v->user?->name ?? '—' }}</small></td>
                        <td><span class="spa-badge">{{ ucfirst($v->metodo_pago) }}</span></td>
                        <td><strong>{{ $sim }} {{ number_format($v->total, 2) }}</strong></td>
                        <td>
                            @if($v->estado === 'pagada')<span class="spa-badge success">Pagada</span>
                            @elseif($v->estado === 'anulada')<span class="spa-badge danger">Anulada</span>
                            @else<span class="spa-badge warning">Pendiente</span>@endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('ventas.show', $v) }}" class="btn btn-sm" style="background:var(--spa-info);color:#fff"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $ventas->links() }}</div>
    @endif
</div>
@endsection
