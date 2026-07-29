@extends('layouts.app')
@section('titulo', 'Acceso denegado')
@section('contenido')
    <div class="spa-card text-center" style="padding:3rem 1.5rem">
        <i class="bi bi-shield-lock" style="font-size:4rem;color:var(--spa-primary)"></i>
        <h2 class="mt-3" style="color:var(--spa-secondary)">403 — Acceso denegado</h2>
        <p class="text-spa-muted">{{ $exception->getMessage() ?: 'No tienes permiso para acceder a esta sección.' }}</p>
        <a href="{{ route('dashboard') }}" class="btn btn-spa-primary">
            <i class="bi bi-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
@endsection
