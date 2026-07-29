@extends('layouts.app')
@section('titulo', $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $proveedor->exists ? route('proveedores.update', $proveedor) : route('proveedores.store') }}">
    @csrf @if($proveedor->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-truck text-spa-primary"></i> {{ $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor' }}</h3>
            <a href="{{ route('proveedores.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" required value="{{ old('nombre', $proveedor->nombre) }}"></div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="activo" id="act" class="form-check-input" value="1" {{ old('activo', $proveedor->activo ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="act">Activo</label>
                </div>
            </div>
            <div class="col-md-6"><label class="form-label">Persona de contacto</label><input type="text" name="contacto" class="form-control" value="{{ old('contacto', $proveedor->contacto) }}"></div>
            <div class="col-md-6"><label class="form-label">Teléfono</label><input type="text" name="telefono" class="form-control" value="{{ old('telefono', $proveedor->telefono) }}"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $proveedor->email) }}"></div>
            <div class="col-md-6"><label class="form-label">Dirección</label><input type="text" name="direccion" class="form-control" value="{{ old('direccion', $proveedor->direccion) }}"></div>
            <div class="col-12"><label class="form-label">Notas</label><textarea name="notas" class="form-control" rows="3">{{ old('notas', $proveedor->notas) }}</textarea></div>
        </div>
        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('proveedores.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </div>
</form>
@endsection
