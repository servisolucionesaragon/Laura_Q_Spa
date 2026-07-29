@extends('layouts.app')
@section('titulo', 'Vender bono')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ route('bonos.store') }}">
    @csrf
    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-gift text-spa-primary"></i> Vender bono</h3>
            <a href="{{ route('bonos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Cliente *</label>
                <select name="cliente_id" class="form-select" required>
                    <option value="">— Selecciona cliente —</option>
                    @foreach($clientes as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->nombre_completo }} {{ $cl->telefono ? '· ' . $cl->telefono : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Plantilla de bono *</label>
                <select name="plantilla_id" id="plantilla" class="form-select" required onchange="actualizarPrecio()">
                    <option value="">— Selecciona bono —</option>
                    @foreach($plantillas as $p)
                        <option value="{{ $p->id }}" data-precio="{{ $p->precio }}" data-sesiones="{{ $p->sesiones_total }}" data-validez="{{ $p->validez_dias }}">
                            {{ $p->nombre }} · {{ $p->sesiones_total }} sesiones · Q {{ number_format($p->precio, 2) }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text" id="info"></small>
            </div>
            <div class="col-md-4"><label class="form-label">Fecha de compra *</label><input type="date" name="fecha_compra" class="form-control" required value="{{ now()->format('Y-m-d') }}"></div>
            <div class="col-md-4"><label class="form-label">Precio pagado *</label><input type="number" step="0.01" name="precio_pagado" id="precio" class="form-control" required min="0"></div>
            <div class="col-12"><label class="form-label">Notas</label><textarea name="notas" class="form-control" rows="2"></textarea></div>
        </div>
        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('bonos.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-save"></i> Vender bono</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function actualizarPrecio() {
    const sel = document.getElementById('plantilla');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.precio) {
        document.getElementById('precio').value = opt.dataset.precio;
        document.getElementById('info').innerHTML = opt.dataset.sesiones + ' sesiones · vence en ' + opt.dataset.validez + ' días';
    }
}
</script>
@endpush
