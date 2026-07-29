@extends('layouts.app')
@section('titulo', 'Categorías de tratamientos')

@section('contenido')
@include('layouts.partials.errors')

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-tags text-spa-primary"></i> Categorías de tratamientos</h3>
            <small class="text-spa-muted">Agrupa tus tratamientos para una mejor organización.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tratamientos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Tratamientos</a>
            <button class="btn btn-spa-primary" data-bs-toggle="modal" data-bs-target="#mCat" onclick="abrirCrear()">
                <i class="bi bi-plus-lg"></i> Nueva categoría
            </button>
        </div>
    </div>

    @if($categorias->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-tag" style="font-size:2.5rem;opacity:.4"></i><p class="mt-2">Sin categorías.</p></div>
    @else
        <div class="row g-3">
        @foreach($categorias as $c)
            <div class="col-md-6 col-xl-4">
                <div class="spa-stat" style="padding:1.2rem">
                    <div style="display:flex;align-items:center;gap:.7rem;margin-bottom:.6rem">
                        <div style="width:42px;height:42px;border-radius:10px;background:{{ $c->color }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem">
                            <i class="bi bi-{{ $c->icono ?? 'flower2' }}"></i>
                        </div>
                        <div>
                            <strong style="color:var(--spa-secondary);font-size:1.05rem">{{ $c->nombre }}</strong>
                            <div style="font-size:.78rem;color:var(--spa-muted)">{{ $c->tratamientos_count }} tratamientos</div>
                        </div>
                    </div>
                    @if($c->descripcion)<p style="font-size:.85rem;color:var(--spa-muted);margin-bottom:.7rem">{{ $c->descripcion }}</p>@endif
                    <div class="d-flex gap-1">
                        <button class="btn btn-spa-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#mCat" onclick='abrirEditar(@json($c))'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('categorias-tratamientos.destroy', $c) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                        </form>
                        @if($c->activo)<span class="spa-badge success ms-auto">Activa</span>
                        @else<span class="spa-badge danger ms-auto">Inactiva</span>@endif
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    @endif
</div>

<div class="modal fade" id="mCat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formCat" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="ttl"><i class="bi bi-tag"></i> Nueva categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_method" id="met" value="POST">
                <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="cN" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Descripción</label><input type="text" name="descripcion" id="cD" class="form-control"></div>
                <div class="row g-3">
                    <div class="col-6"><label class="form-label">Color</label><input type="color" name="color" id="cC" class="form-control form-control-color" value="#d4a5c0"></div>
                    <div class="col-6">
                        <label class="form-label">Ícono</label>
                        <select name="icono" id="cI" class="form-select">
                            @foreach(['flower2','flower1','flower3','gem','stars','heart','heart-fill','droplet','sparkles','palette','palette2','scissors','hand-thumbs-up','eye','water'] as $ic)
                                <option value="{{ $ic }}">{{ $ic }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-check mt-3"><input type="checkbox" name="activo" id="cA" class="form-check-input" value="1" checked><label class="form-check-label" for="cA">Activa</label></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-spa-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function abrirCrear() {
    document.getElementById('formCat').action = "{{ route('categorias-tratamientos.store') }}";
    document.getElementById('met').value = 'POST';
    document.getElementById('ttl').innerHTML = '<i class="bi bi-tag"></i> Nueva categoría';
    document.getElementById('cN').value = ''; document.getElementById('cD').value = '';
    document.getElementById('cC').value = '#d4a5c0'; document.getElementById('cI').value = 'flower2';
    document.getElementById('cA').checked = true;
}
function abrirEditar(c) {
    document.getElementById('formCat').action = "{{ url('categorias-tratamientos') }}/" + c.id;
    document.getElementById('met').value = 'PUT';
    document.getElementById('ttl').innerHTML = '<i class="bi bi-pencil"></i> Editar categoría';
    document.getElementById('cN').value = c.nombre; document.getElementById('cD').value = c.descripcion || '';
    document.getElementById('cC').value = c.color || '#d4a5c0'; document.getElementById('cI').value = c.icono || 'flower2';
    document.getElementById('cA').checked = !!c.activo;
}
</script>
@endpush
