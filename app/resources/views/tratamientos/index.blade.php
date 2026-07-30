@extends('layouts.app')
@section('titulo', 'Servicios')

@section('contenido')
<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-flower2 text-spa-primary"></i> Servicios</h3>
            <small class="text-spa-muted">Catálogo de servicios con duración, precio y comisión.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('categorias-tratamientos.index') }}" class="btn btn-spa-secondary">
                <i class="bi bi-tags"></i> Categorías
            </a>
            <a href="{{ route('tratamientos.create') }}" class="btn btn-spa-primary">
                <i class="bi bi-plus-lg"></i> Nuevo servicio
            </a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-7">
            <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por nombre...">
        </div>
        <div class="col-md-3">
            <select name="categoria_id" class="form-select">
                <option value="">— Todas las categorías —</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ (string) $categoriaId === (string) $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-spa-secondary btn-block"><i class="bi bi-search"></i> Filtrar</button></div>
    </form>

    @if($tratamientos->isEmpty())
        <div class="text-center py-4 text-spa-muted">
            <i class="bi bi-flower3" style="font-size:2.5rem;opacity:.4"></i>
            <p class="mt-2">No hay servicios.</p>
        </div>
    @else
        @php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp
        <div class="table-responsive">
            <table class="spa-table">
                <thead>
                    <tr><th>Categoría</th><th>Nombre</th><th>Duración</th><th>Precio</th><th>Comisión</th><th>Estado</th><th class="text-end">Acciones</th></tr>
                </thead>
                <tbody>
                @foreach($tratamientos as $t)
                    <tr>
                        <td>
                            @if($t->categoria)
                                <span class="spa-badge" style="background:{{ $t->categoria->color }};color:#fff">
                                    {{ $t->categoria->nombre }}
                                </span>
                            @else
                                <span class="text-spa-muted">—</span>
                            @endif
                        </td>
                        <td><strong>{{ $t->nombre }}</strong></td>
                        <td>{{ $t->duracion_min }} min</td>
                        <td><strong>{{ $sim }} {{ number_format($t->precio, 2) }}</strong></td>
                        <td>{{ $t->comision_porcentaje }}%</td>
                        <td>
                            @if($t->activo)<span class="spa-badge success">Activo</span>
                            @else<span class="spa-badge danger">Inactivo</span>@endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('tratamientos.edit', $t) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('tratamientos.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el servicio {{ $t->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $tratamientos->links() }}</div>
    @endif
</div>
@endsection
