@extends('layouts.app')
@section('titulo', 'Clientes')

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-people text-spa-primary"></i> Clientes</h3>
            <small class="text-spa-muted">Base de datos de clientes del centro.</small>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn btn-spa-primary"><i class="bi bi-person-plus"></i> Nuevo cliente</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-10"><input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por nombre, apellido, teléfono o email..."></div>
        <div class="col-md-2"><button class="btn btn-spa-secondary btn-block"><i class="bi bi-search"></i> Buscar</button></div>
    </form>

    @if($clientes->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-people" style="font-size:2.5rem;opacity:.4"></i><p>Sin clientes.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Cliente</th><th>Contacto</th><th>Citas</th><th>Bonos</th><th>Ventas</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($clientes as $c)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--spa-primary),var(--spa-accent));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">
                                    {{ strtoupper(substr($c->nombre, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $c->nombre_completo }}</strong>
                                    @if($c->fecha_nacimiento)<div style="font-size:.78rem;color:var(--spa-muted)">{{ $c->edad }} años</div>@endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($c->telefono)<div><i class="bi bi-telephone"></i> {{ $c->telefono }}</div>@endif
                            @if($c->email)<small class="text-spa-muted"><i class="bi bi-envelope"></i> {{ $c->email }}</small>@endif
                        </td>
                        <td><span class="spa-badge">{{ $c->citas_count }}</span></td>
                        <td><span class="spa-badge warning">{{ $c->bonos_count }}</span></td>
                        <td><span class="spa-badge success">{{ $c->ventas_count }}</span></td>
                        <td>@if($c->activo)<span class="spa-badge success">Activo</span>@else<span class="spa-badge danger">Inactivo</span>@endif</td>
                        <td class="text-end" style="white-space:nowrap">
                            <a href="{{ route('clientes.show', $c) }}" class="btn btn-sm" style="background:var(--spa-info);color:#fff" title="Ficha"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('clientes.edit', $c) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('clientes.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $clientes->links() }}</div>
    @endif
</div>
@endsection
