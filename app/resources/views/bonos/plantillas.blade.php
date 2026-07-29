@extends('layouts.app')
@section('titulo', 'Plantillas de bonos')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-tags text-spa-primary"></i> Plantillas de bonos</h3>
            <small class="text-spa-muted">Define los paquetes de servicios que vendes a tus clientes.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bonos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-gift"></i> Bonos vendidos</a>
            <a href="{{ route('bonos-plantillas.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Nueva plantilla</a>
        </div>
    </div>

    @if($plantillas->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-tag" style="font-size:2.5rem;opacity:.4"></i><p>Sin plantillas.</p></div>
    @else
        <div class="row g-3">
        @foreach($plantillas as $p)
            <div class="col-md-6 col-xl-4">
                <div class="spa-card" style="margin:0;height:100%;display:flex;flex-direction:column">
                    <div style="font-size:.78rem;color:var(--spa-muted);letter-spacing:.5px;text-transform:uppercase;font-weight:600">{{ $p->tratamiento?->nombre ?? 'Bono general' }}</div>
                    <h4 style="color:var(--spa-secondary);margin:.4rem 0">{{ $p->nombre }}</h4>
                    @if($p->descripcion)<p style="color:var(--spa-muted);font-size:.88rem;margin-bottom:.8rem">{{ $p->descripcion }}</p>@endif
                    <div style="flex:1"></div>
                    <div class="d-flex justify-content-between align-items-center" style="border-top:1px solid var(--spa-border-soft);padding-top:.75rem;margin-top:.5rem">
                        <div>
                            <div style="font-size:1.6rem;font-weight:800;color:var(--spa-primary-dark)">{{ $sim }} {{ number_format($p->precio, 2) }}</div>
                            <div style="font-size:.8rem;color:var(--spa-muted)">{{ $p->sesiones_total }} sesiones · {{ $p->validez_dias }} días</div>
                        </div>
                        <div>
                            @if($p->activo)<span class="spa-badge success">Activa</span>@else<span class="spa-badge danger">Inactiva</span>@endif
                        </div>
                    </div>
                    <div style="font-size:.78rem;color:var(--spa-muted);margin-top:.4rem">{{ $p->bonos_count }} vendidos</div>
                    <div class="d-flex gap-1 mt-2">
                        <a href="{{ route('bonos-plantillas.edit', $p) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('bonos-plantillas.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    @endif
</div>
@endsection
