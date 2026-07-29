@extends('layouts.app')
@section('titulo', 'Categorías de productos')

@section('contenido')
@include('layouts.partials.errors')

<div class="spa-card">
    <div class="spa-card-header">
        <div><h3><i class="bi bi-tags text-spa-primary"></i> Categorías de productos</h3></div>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Productos</a>
            <button class="btn btn-spa-primary" data-bs-toggle="modal" data-bs-target="#mCat" onclick="abrirCrear()"><i class="bi bi-plus-lg"></i> Nueva</button>
        </div>
    </div>

    @if($categorias->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-tag" style="font-size:2.5rem;opacity:.4"></i><p>Sin categorías.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Nombre</th><th>Productos</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($categorias as $c)
                    <tr>
                        <td><strong>{{ $c->nombre }}</strong></td>
                        <td><span class="spa-badge">{{ $c->productos_count }}</span></td>
                        <td>@if($c->activo)<span class="spa-badge success">Activa</span>@else<span class="spa-badge danger">Inactiva</span>@endif</td>
                        <td class="text-end">
                            <button class="btn btn-spa-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#mCat" onclick='abrirEditar(@json($c))'><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('categorias-productos.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="modal fade" id="mCat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="f" class="modal-content">
            @csrf <input type="hidden" name="_method" id="met" value="POST">
            <div class="modal-header"><h5 class="modal-title" id="t"><i class="bi bi-tag"></i> Nueva categoría</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="cN" class="form-control" required></div>
                <div class="form-check"><input type="checkbox" name="activo" id="cA" class="form-check-input" value="1" checked><label class="form-check-label" for="cA">Activa</label></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-spa-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function abrirCrear() {
    document.getElementById('f').action = "{{ route('categorias-productos.store') }}";
    document.getElementById('met').value = 'POST';
    document.getElementById('t').innerHTML = '<i class="bi bi-tag"></i> Nueva categoría';
    document.getElementById('cN').value = ''; document.getElementById('cA').checked = true;
}
function abrirEditar(c) {
    document.getElementById('f').action = "{{ url('categorias-productos') }}/" + c.id;
    document.getElementById('met').value = 'PUT';
    document.getElementById('t').innerHTML = '<i class="bi bi-pencil"></i> Editar categoría';
    document.getElementById('cN').value = c.nombre; document.getElementById('cA').checked = !!c.activo;
}
</script>
@endpush
