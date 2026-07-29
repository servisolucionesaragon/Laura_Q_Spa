@extends('layouts.app')
@section('titulo', 'Proveedores')

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-truck text-spa-primary"></i> Proveedores</h3>
            <small class="text-spa-muted">Empresas e individuos que abastecen tus productos.</small>
        </div>
        <a href="{{ route('proveedores.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Nuevo proveedor</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-10"><input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar..."></div>
        <div class="col-md-2"><button class="btn btn-spa-secondary btn-block"><i class="bi bi-search"></i> Filtrar</button></div>
    </form>

    @if($proveedores->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-truck" style="font-size:2.5rem;opacity:.4"></i><p>Sin proveedores.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Nombre</th><th>Contacto</th><th>Teléfono</th><th>Email</th><th>Productos</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($proveedores as $p)
                    <tr>
                        <td><strong>{{ $p->nombre }}</strong></td>
                        <td>{{ $p->contacto ?? '—' }}</td>
                        <td>{{ $p->telefono ?? '—' }}</td>
                        <td><small>{{ $p->email ?? '—' }}</small></td>
                        <td><span class="spa-badge">{{ $p->productos_count }}</span></td>
                        <td>@if($p->activo)<span class="spa-badge success">Activo</span>@else<span class="spa-badge danger">Inactivo</span>@endif</td>
                        <td class="text-end">
                            <a href="{{ route('proveedores.edit', $p) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('proveedores.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $proveedores->links() }}</div>
    @endif
</div>
@endsection
