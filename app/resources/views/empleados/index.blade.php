@extends('layouts.app')
@section('titulo', 'Empleados')

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-person-badge text-spa-primary"></i> Empleados</h3>
            <small class="text-spa-muted">Personal del centro: profesionales, recepción, caja y administración.</small>
        </div>
        <a href="{{ route('empleados.create') }}" class="btn btn-spa-primary">
            <i class="bi bi-plus-lg"></i> Nuevo empleado
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por nombre o correo...">
        </div>
        <div class="col-md-4">
            <select name="rol" class="form-select">
                <option value="">— Todos los roles —</option>
                @foreach(['admin'=>'Administrador','recepcionista'=>'Recepcionista','profesional'=>'Profesional','cajero'=>'Cajero'] as $v=>$l)
                    <option value="{{ $v }}" {{ $rol === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-spa-secondary btn-block"><i class="bi bi-search"></i> Filtrar</button>
        </div>
    </form>

    @if($empleados->isEmpty())
        <div class="text-center py-4 text-spa-muted">
            <i class="bi bi-person-x" style="font-size:2.5rem;opacity:.4"></i>
            <p class="mt-2">Sin empleados que coincidan con los filtros.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead>
                    <tr><th>Nombre</th><th>Rol</th><th>Email</th><th>Teléfono</th><th>Estado</th><th class="text-end">Acciones</th></tr>
                </thead>
                <tbody>
                @foreach($empleados as $e)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--spa-primary),var(--spa-accent));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">
                                    {{ strtoupper(substr($e->name, 0, 1)) }}
                                </div>
                                <strong>{{ $e->name }}</strong>
                            </div>
                        </td>
                        <td><span class="spa-badge">{{ $e->rol_nombre }}</span></td>
                        <td><small>{{ $e->email }}</small></td>
                        <td>{{ $e->telefono ?? '—' }}</td>
                        <td>
                            @if($e->activo)
                                <span class="spa-badge success">Activo</span>
                            @else
                                <span class="spa-badge danger">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('empleados.edit', $e) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            @if($e->id !== auth()->id())
                                <form action="{{ route('empleados.destroy', $e) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar a {{ $e->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $empleados->links() }}</div>
    @endif
</div>
@endsection
