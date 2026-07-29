@extends('layouts.app')
@section('titulo', $empleado->exists ? 'Editar empleado' : 'Nuevo empleado')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $empleado->exists ? route('empleados.update', $empleado) : route('empleados.store') }}">
    @csrf
    @if($empleado->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-person-badge text-spa-primary"></i> {{ $empleado->exists ? 'Editar empleado' : 'Nuevo empleado' }}</h3>
            <a href="{{ route('empleados.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="name" class="form-control" required maxlength="191"
                       value="{{ old('name', $empleado->name) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Rol *</label>
                <select name="rol" class="form-select" required>
                    @foreach(['admin'=>'Administrador','recepcionista'=>'Recepcionista','profesional'=>'Profesional','cajero'=>'Cajero'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('rol', $empleado->rol) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo electrónico *</label>
                <input type="email" name="email" class="form-control" required maxlength="191"
                       value="{{ old('email', $empleado->email) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" maxlength="30"
                       value="{{ old('telefono', $empleado->telefono) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ $empleado->exists ? 'Nueva contraseña (opcional)' : 'Contraseña *' }}</label>
                <input type="password" name="password" class="form-control" {{ $empleado->exists ? '' : 'required' }} minlength="6"
                       placeholder="{{ $empleado->exists ? 'Dejar en blanco para no cambiar' : 'Mínimo 6 caracteres' }}">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1"
                           {{ old('activo', $empleado->activo ?? true) ? 'checked' : '' }}>
                    <label for="activo" class="form-check-label">Empleado activo</label>
                </div>
            </div>
        </div>

        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('empleados.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button type="submit" class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </div>
</form>
@endsection
