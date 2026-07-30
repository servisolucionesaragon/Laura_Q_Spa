@extends('layouts.app')
@section('titulo', $tratamiento->exists ? 'Editar servicio' : 'Nuevo servicio')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $tratamiento->exists ? route('tratamientos.update', $tratamiento) : route('tratamientos.store') }}">
    @csrf
    @if($tratamiento->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-flower2 text-spa-primary"></i> {{ $tratamiento->exists ? 'Editar servicio' : 'Nuevo servicio' }}</h3>
            <a href="{{ route('tratamientos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control" required maxlength="191"
                       value="{{ old('nombre', $tratamiento->nombre) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Categoría</label>
                <select name="categoria_id" class="form-select">
                    <option value="">— Sin categoría —</option>
                    @foreach($categorias as $c)
                        <option value="{{ $c->id }}" {{ (string) old('categoria_id', $tratamiento->categoria_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Duración (min) *</label>
                <input type="number" name="duracion_min" class="form-control" required min="5" max="480"
                       value="{{ old('duracion_min', $tratamiento->duracion_min) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Precio *</label>
                <input type="number" step="0.01" name="precio" class="form-control" required min="0"
                       value="{{ old('precio', $tratamiento->precio) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Comisión (%)</label>
                <input type="number" step="0.01" name="comision_porcentaje" class="form-control" min="0" max="100"
                       value="{{ old('comision_porcentaje', $tratamiento->comision_porcentaje) }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-3">
                <div class="form-check">
                    <input type="checkbox" name="requiere_cabina" id="rc" class="form-check-input" value="1"
                           {{ old('requiere_cabina', $tratamiento->requiere_cabina) ? 'checked' : '' }}>
                    <label for="rc" class="form-check-label">Requiere cabina</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="activo" id="act" class="form-check-input" value="1"
                           {{ old('activo', $tratamiento->activo ?? true) ? 'checked' : '' }}>
                    <label for="act" class="form-check-label">Activo</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $tratamiento->descripcion) }}</textarea>
            </div>
        </div>

        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('tratamientos.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button type="submit" class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </div>
</form>
@endsection
