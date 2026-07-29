@extends('layouts.app')
@section('titulo', 'Movimientos de stock')

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-clock-history text-spa-primary"></i> Movimientos · {{ $producto->nombre }}</h3>
            <small class="text-spa-muted">Stock actual: <strong>{{ $producto->stock_actual }} {{ $producto->unidad }}</strong></small>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>

    @if($movimientos->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-clock" style="font-size:2.5rem;opacity:.4"></i><p>Sin movimientos.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Stock anterior</th><th>Stock nuevo</th><th>Motivo</th><th>Usuario</th></tr></thead>
                <tbody>
                @foreach($movimientos as $m)
                    <tr>
                        <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @switch($m->tipo)
                                @case('entrada')<span class="spa-badge success"><i class="bi bi-arrow-down"></i> Entrada</span>@break
                                @case('salida')<span class="spa-badge danger"><i class="bi bi-arrow-up"></i> Salida</span>@break
                                @case('ajuste')<span class="spa-badge warning"><i class="bi bi-pencil"></i> Ajuste</span>@break
                            @endswitch
                        </td>
                        <td><strong>{{ $m->cantidad }}</strong></td>
                        <td>{{ $m->stock_anterior }}</td>
                        <td><strong>{{ $m->stock_nuevo }}</strong></td>
                        <td>{{ $m->motivo ?? '—' }}</td>
                        <td><small>{{ $m->user?->name ?? '—' }}</small></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $movimientos->links() }}</div>
    @endif
</div>
@endsection
