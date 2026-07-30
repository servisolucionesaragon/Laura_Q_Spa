@extends('layouts.app')
@section('titulo', 'Métodos de pago')

@section('contenido')
@include('layouts.partials.errors')

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-credit-card text-spa-primary"></i> Métodos de pago</h3>
            <small class="text-spa-muted">Opciones disponibles al cobrar en el Punto de Venta.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Ventas</a>
            <button class="btn btn-spa-primary" data-bs-toggle="modal" data-bs-target="#mMetodo" onclick="abrirCrear()"><i class="bi bi-plus-lg"></i> Nuevo</button>
        </div>
    </div>

    @if($metodosPago->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-credit-card-2-front" style="font-size:2.5rem;opacity:.4"></i><p>Sin métodos de pago.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Nombre</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($metodosPago as $m)
                    <tr>
                        <td><strong>{{ ucfirst($m->nombre) }}</strong></td>
                        <td>@if($m->activo)<span class="spa-badge success">Activo</span>@else<span class="spa-badge danger">Inactivo</span>@endif</td>
                        <td class="text-end">
                            <button class="btn btn-spa-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#mMetodo" onclick='abrirEditar(@json($m))'><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('metodos-pago.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este método de pago?')">
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

    <div class="form-text mt-2">
        <i class="bi bi-info-circle"></i> Solo los métodos <strong>activos</strong> aparecen para elegir en el
        Punto de Venta. Desactiva uno en vez de eliminarlo si ya se usó en ventas anteriores.
    </div>
</div>

<div class="modal fade" id="mMetodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="f" class="modal-content">
            @csrf <input type="hidden" name="_method" id="met" value="POST">
            <div class="modal-header"><h5 class="modal-title" id="t"><i class="bi bi-credit-card"></i> Nuevo método de pago</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="cN" class="form-control" required></div>
                <div class="form-check"><input type="checkbox" name="activo" id="cA" class="form-check-input" value="1" checked><label class="form-check-label" for="cA">Activo</label></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-spa-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function abrirCrear() {
    document.getElementById('f').action = "{{ route('metodos-pago.store') }}";
    document.getElementById('met').value = 'POST';
    document.getElementById('t').innerHTML = '<i class="bi bi-credit-card"></i> Nuevo método de pago';
    document.getElementById('cN').value = ''; document.getElementById('cA').checked = true;
}
function abrirEditar(m) {
    document.getElementById('f').action = "{{ url('metodos-pago') }}/" + m.id;
    document.getElementById('met').value = 'PUT';
    document.getElementById('t').innerHTML = '<i class="bi bi-pencil"></i> Editar método de pago';
    document.getElementById('cN').value = m.nombre; document.getElementById('cA').checked = !!m.activo;
}
</script>
@endpush
