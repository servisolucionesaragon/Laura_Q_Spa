@extends('layouts.app')
@section('titulo', $cliente->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $cliente->exists ? route('clientes.update', $cliente) : route('clientes.store') }}">
    @csrf @if($cliente->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-person-vcard text-spa-primary"></i> {{ $cliente->exists ? 'Editar cliente' : 'Nuevo cliente' }}</h3>
            <a href="{{ route('clientes.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <h5 style="color:var(--spa-secondary);margin-bottom:1rem">Datos personales</h5>
        <div class="row g-3 mb-3">
            <div class="col-md-6"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" required value="{{ old('nombre', $cliente->nombre) }}"></div>
            <div class="col-md-6"><label class="form-label">Apellido</label><input type="text" name="apellido" class="form-control" value="{{ old('apellido', $cliente->apellido) }}"></div>
            <div class="col-md-4"><label class="form-label">Teléfono</label><input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}"></div>
            <div class="col-md-5"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email) }}"></div>
            <div class="col-md-3"><label class="form-label">Documento</label><input type="text" name="documento" class="form-control" value="{{ old('documento', $cliente->documento) }}"></div>
            <div class="col-md-4"><label class="form-label">Fecha de nacimiento</label><input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $cliente->fecha_nacimiento?->format('Y-m-d')) }}"></div>
            <div class="col-md-4"><label class="form-label">Género</label>
                <select name="genero" class="form-select">
                    <option value="">— No especifica —</option>
                    <option value="F" {{ old('genero', $cliente->genero) === 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="M" {{ old('genero', $cliente->genero) === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="O" {{ old('genero', $cliente->genero) === 'O' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">¿Cómo nos conoció?</label><input type="text" name="como_nos_conocio" class="form-control" value="{{ old('como_nos_conocio', $cliente->como_nos_conocio) }}" placeholder="Instagram, recomendación..."></div>
        </div>

        <h5 style="color:var(--spa-secondary);margin-bottom:1rem">Dirección</h5>
        <div class="row g-3 mb-3">
            <div class="col-md-8"><label class="form-label">Dirección</label><input type="text" name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion) }}"></div>
            <div class="col-md-4"><label class="form-label">Ciudad</label><input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $cliente->ciudad) }}"></div>
        </div>

        <h5 style="color:var(--spa-secondary);margin-bottom:1rem">Información médica y notas</h5>
        <div class="row g-3 mb-3">
            <div class="col-md-6"><label class="form-label">Alergias / contraindicaciones</label><textarea name="alergias" class="form-control" rows="3" placeholder="Productos, sustancias, condiciones médicas...">{{ old('alergias', $cliente->alergias) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Notas internas</label><textarea name="notas" class="form-control" rows="3">{{ old('notas', $cliente->notas) }}</textarea></div>
        </div>

        <div class="d-flex gap-3 flex-wrap">
            <div class="form-check"><input type="checkbox" name="acepta_marketing" id="am" class="form-check-input" value="1" {{ old('acepta_marketing', $cliente->acepta_marketing) ? 'checked' : '' }}><label for="am" class="form-check-label">Acepta recibir promociones</label></div>
            <div class="form-check"><input type="checkbox" name="activo" id="ac" class="form-check-input" value="1" {{ old('activo', $cliente->activo ?? true) ? 'checked' : '' }}><label for="ac" class="form-check-label">Cliente activo</label></div>
        </div>

        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('clientes.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </div>
</form>
@endsection
