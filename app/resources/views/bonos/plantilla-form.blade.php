@extends('layouts.app')
@section('titulo', $plantilla->exists ? 'Editar plantilla' : 'Nueva plantilla de bono')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $plantilla->exists ? route('bonos-plantillas.update', $plantilla) : route('bonos-plantillas.store') }}">
    @csrf @if($plantilla->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-tag text-spa-primary"></i> {{ $plantilla->exists ? 'Editar plantilla' : 'Nueva plantilla' }}</h3>
            <a href="{{ route('bonos-plantillas.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" required value="{{ old('nombre', $plantilla->nombre) }}"></div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check"><input type="checkbox" name="activo" id="ac" class="form-check-input" value="1" {{ old('activo', $plantilla->activo ?? true) ? 'checked' : '' }}><label for="ac" class="form-check-label">Activa</label></div>
            </div>
            <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $plantilla->descripcion) }}</textarea></div>
            <div class="col-md-6">
                <label class="form-label">Servicio asociado</label>
                <select name="tratamiento_id" class="form-select">
                    <option value="">— Sin servicio específico —</option>
                    @foreach($tratamientos as $t)
                        <option value="{{ $t->id }}" {{ (string) old('tratamiento_id', $plantilla->tratamiento_id) === (string) $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Sesiones *</label><input type="number" name="sesiones_total" class="form-control" required min="1" value="{{ old('sesiones_total', $plantilla->sesiones_total) }}"></div>
            <div class="col-md-2"><label class="form-label">Validez (días) *</label><input type="number" name="validez_dias" class="form-control" required min="1" value="{{ old('validez_dias', $plantilla->validez_dias) }}"></div>
            <div class="col-md-2"><label class="form-label">Precio *</label><input type="number" step="0.01" name="precio" class="form-control" required min="0" value="{{ old('precio', $plantilla->precio) }}"></div>
        </div>
        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('bonos-plantillas.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </div>
</form>
@endsection
