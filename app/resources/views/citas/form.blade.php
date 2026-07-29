@extends('layouts.app')
@section('titulo', $cita->exists ? 'Editar cita' : 'Nueva cita')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $cita->exists ? route('citas.update', $cita) : route('citas.store') }}">
    @csrf @if($cita->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-calendar-plus text-spa-primary"></i> {{ $cita->exists ? 'Editar cita' : 'Nueva cita' }}</h3>
            <a href="{{ route('citas.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Cliente *</label>
                <select name="cliente_id" class="form-select" required>
                    <option value="">— Selecciona cliente —</option>
                    @foreach($clientes as $cl)
                        <option value="{{ $cl->id }}" {{ (string) old('cliente_id', $cita->cliente_id) === (string) $cl->id ? 'selected' : '' }}>
                            {{ $cl->nombre_completo }} {{ $cl->telefono ? '· ' . $cl->telefono : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tratamiento *</label>
                <select name="tratamiento_id" class="form-select" required>
                    <option value="">— Selecciona servicio —</option>
                    @php $serviciosCita = $cita->servicios()->first(); @endphp
                    @foreach($tratamientos as $t)
                        <option value="{{ $t->id }}" {{ (string) old('tratamiento_id', $serviciosCita?->tratamiento_id) === (string) $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }} · {{ $t->duracion_min }} min · Q {{ number_format($t->precio, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha *</label>
                <input type="date" name="fecha" class="form-control" required value="{{ old('fecha', $cita->fecha?->format('Y-m-d') ?? $cita->fecha) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Hora inicio *</label>
                <input type="time" name="hora_inicio" class="form-control" required value="{{ old('hora_inicio', \Carbon\Carbon::parse($cita->hora_inicio ?? '10:00')->format('H:i')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado *</label>
                <select name="estado" class="form-select" required>
                    @foreach(['pendiente'=>'Pendiente','confirmada'=>'Confirmada','realizada'=>'Realizada','cancelada'=>'Cancelada','no_show'=>'No asistió'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('estado', $cita->estado ?? 'pendiente') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Profesional</label>
                <select name="profesional_id" class="form-select">
                    <option value="">— Sin asignar —</option>
                    @foreach($profesionales as $p)
                        <option value="{{ $p->id }}" {{ (string) old('profesional_id', $cita->profesional_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Cabina</label>
                <select name="cabina_id" class="form-select">
                    <option value="">— Sin asignar —</option>
                    @foreach($cabinas as $c)
                        <option value="{{ $c->id }}" {{ (string) old('cabina_id', $cita->cabina_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Notas</label>
                <textarea name="notas" class="form-control" rows="3">{{ old('notas', $cita->notas) }}</textarea>
            </div>
        </div>

        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('citas.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar cita</button>
        </div>
    </div>
</form>
@endsection
