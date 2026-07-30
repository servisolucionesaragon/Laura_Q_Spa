@extends('layouts.app')
@section('titulo', 'Cabinas')

@section('contenido')
@include('layouts.partials.errors')

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-door-open text-spa-primary"></i> Cabinas</h3>
            <small class="text-spa-muted">Gestiona los espacios donde se realizan los servicios.</small>
        </div>
        <button type="button" class="btn btn-spa-primary" data-bs-toggle="modal" data-bs-target="#mCabina"
                onclick="abrirCrear()">
            <i class="bi bi-plus-lg"></i> Nueva cabina
        </button>
    </div>

    @if($cabinas->isEmpty())
        <div class="text-center py-4 text-spa-muted">
            <i class="bi bi-door-closed" style="font-size:2.5rem;opacity:.4"></i>
            <p class="mt-2">No hay cabinas registradas todavía.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead>
                    <tr>
                        <th>Color</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($cabinas as $c)
                    <tr>
                        <td><span style="display:inline-block;width:24px;height:24px;border-radius:6px;background:{{ $c->color }};border:1px solid var(--spa-border)"></span></td>
                        <td><strong>{{ $c->nombre }}</strong></td>
                        <td>{{ $c->descripcion ?? '—' }}</td>
                        <td>
                            @if($c->activo)
                                <span class="spa-badge success">Activa</span>
                            @else
                                <span class="spa-badge danger">Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button class="btn btn-spa-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#mCabina"
                                    onclick='abrirEditar(@json($c))'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('cabinas.destroy', $c) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar la cabina {{ $c->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Modal --}}
<div class="modal fade" id="mCabina" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formCabina" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal"><i class="bi bi-door-open"></i> Nueva cabina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_method" id="metodoInput" value="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" id="cNombre" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" id="cDescripcion" class="form-control" maxlength="255">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" id="cColor" class="form-control form-control-color" value="#d4a5c0">
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="activo" id="cActivo" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="cActivo">Activa</label>
                        </div>
                    </div>
                </div>
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
    document.getElementById('formCabina').action = "{{ route('cabinas.store') }}";
    document.getElementById('metodoInput').value = 'POST';
    document.getElementById('tituloModal').innerHTML = '<i class="bi bi-door-open"></i> Nueva cabina';
    document.getElementById('cNombre').value = '';
    document.getElementById('cDescripcion').value = '';
    document.getElementById('cColor').value = '#d4a5c0';
    document.getElementById('cActivo').checked = true;
}
function abrirEditar(c) {
    document.getElementById('formCabina').action = "{{ url('cabinas') }}/" + c.id;
    document.getElementById('metodoInput').value = 'PUT';
    document.getElementById('tituloModal').innerHTML = '<i class="bi bi-pencil"></i> Editar cabina';
    document.getElementById('cNombre').value = c.nombre;
    document.getElementById('cDescripcion').value = c.descripcion || '';
    document.getElementById('cColor').value = c.color || '#d4a5c0';
    document.getElementById('cActivo').checked = !!c.activo;
}
</script>
@endpush
