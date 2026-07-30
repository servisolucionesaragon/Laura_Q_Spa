@extends('layouts.app')
@section('titulo', 'Abrir caja')

@section('contenido')
@include('layouts.partials.errors')

<div class="spa-card" style="max-width:520px;margin:0 auto">
    <div class="spa-card-header">
        <h3><i class="bi bi-unlock text-spa-primary"></i> Abrir caja</h3>
    </div>

    <form method="POST" action="{{ route('caja.abrir.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Monto inicial en efectivo *</label>
            <input type="number" step="0.01" min="0" name="monto_apertura" class="form-control" value="{{ old('monto_apertura', 0) }}" required autofocus>
            <div class="form-text">El dinero en efectivo con el que arranca la caja hoy (fondo/base).</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Notas</label>
            <textarea name="notas_apertura" class="form-control" rows="2">{{ old('notas_apertura') }}</textarea>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('caja.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-unlock"></i> Abrir caja</button>
        </div>
    </form>
</div>
@endsection
