@extends('layouts.app')
@section('titulo', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $usuario->exists ? route('usuarios.update', $usuario) : route('usuarios.store') }}">
    @csrf @if($usuario->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-shield-lock text-spa-primary"></i> {{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
            <a href="{{ route('usuarios.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nombre *</label><input type="text" name="name" class="form-control" required value="{{ old('name', $usuario->name) }}"></div>
            <div class="col-md-6"><label class="form-label">Rol *</label>
                <select name="rol" class="form-select" required>
                    @foreach(['admin'=>'Administrador','recepcionista'=>'Recepcionista','profesional'=>'Profesional','cajero'=>'Cajero'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('rol', $usuario->rol) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required value="{{ old('email', $usuario->email) }}"></div>
            <div class="col-md-6"><label class="form-label">Teléfono</label><input type="text" name="telefono" class="form-control" value="{{ old('telefono', $usuario->telefono) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ $usuario->exists ? 'Nueva contraseña (opcional)' : 'Contraseña *' }}</label>
                <input type="password" name="password" class="form-control" {{ $usuario->exists ? '' : 'required' }} minlength="6">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check"><input type="checkbox" name="activo" id="ac" class="form-check-input" value="1" {{ old('activo', $usuario->activo ?? true) ? 'checked' : '' }}><label for="ac" class="form-check-label">Activo</label></div>
            </div>
        </div>
        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('usuarios.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </div>
</form>
@endsection
