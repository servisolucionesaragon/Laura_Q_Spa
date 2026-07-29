@extends('layouts.app')
@section('titulo', 'Bonos vendidos')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-gift text-spa-primary"></i> Bonos vendidos</h3>
            <small class="text-spa-muted">Bonos prepagados de tus clientes.</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('bonos-plantillas.index') }}" class="btn btn-spa-secondary"><i class="bi bi-tags"></i> Plantillas</a>
            <a href="{{ route('bonos.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Vender bono</a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-10">
            <select name="estado" class="form-select" onchange="this.form.submit()">
                <option value="">— Todos los estados —</option>
                @foreach(['activo','agotado','vencido','cancelado'] as $e)
                    <option value="{{ $e }}" {{ $estado === $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($bonos->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-gift" style="font-size:2.5rem;opacity:.4"></i><p>Sin bonos.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Código</th><th>Cliente</th><th>Bono</th><th>Sesiones</th><th>Compra</th><th>Vence</th><th>Pagado</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($bonos as $b)
                    <tr>
                        <td><code>{{ $b->codigo }}</code></td>
                        <td>{{ $b->cliente?->nombre_completo }}</td>
                        <td><small>{{ $b->nombre }}</small></td>
                        <td>
                            <strong>{{ $b->sesiones_usadas }} / {{ $b->sesiones_total }}</strong>
                            @php $pct = $b->sesiones_total > 0 ? ($b->sesiones_usadas / $b->sesiones_total) * 100 : 0; @endphp
                            <div style="height:4px;background:var(--spa-border-soft);border-radius:4px;margin-top:3px">
                                <div style="height:100%;width:{{ $pct }}%;background:var(--spa-primary);border-radius:4px"></div>
                            </div>
                        </td>
                        <td>{{ $b->fecha_compra->format('d/m/Y') }}</td>
                        <td>{{ $b->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $sim }} {{ number_format($b->precio_pagado, 2) }}</td>
                        <td><span class="spa-badge {{ $b->estado === 'activo' ? 'success' : ($b->estado === 'agotado' ? 'warning' : 'danger') }}">{{ ucfirst($b->estado) }}</span></td>
                        <td class="text-end" style="white-space:nowrap">
                            <a href="{{ route('bonos.show', $b) }}" class="btn btn-sm" style="background:var(--spa-info);color:#fff"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('bonos.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $bonos->links() }}</div>
    @endif
</div>
@endsection
