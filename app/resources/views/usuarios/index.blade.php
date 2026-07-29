@extends('layouts.app')
@section('titulo', 'Usuarios del sistema')

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-shield-lock text-spa-primary"></i> Usuarios del sistema</h3>
            <small class="text-spa-muted">Personas con acceso al panel administrativo.</small>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Nuevo usuario</a>
    </div>

    @if($usuarios->isEmpty())
        <div class="text-center py-4 text-spa-muted"><p>Sin usuarios.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($usuarios as $u)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--spa-primary),var(--spa-accent));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                                <strong>{{ $u->name }}</strong>
                                @if($u->id === auth()->id())<span class="spa-badge info">Tú</span>@endif
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td><span class="spa-badge dark">{{ $u->rol_nombre }}</span></td>
                        <td>@if($u->activo)<span class="spa-badge success">Activo</span>@else<span class="spa-badge danger">Inactivo</span>@endif</td>
                        <td class="text-end">
                            <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            @if($u->id !== auth()->id())
                                <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')">
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
        <div class="mt-3">{{ $usuarios->links() }}</div>
    @endif
</div>
@endsection
