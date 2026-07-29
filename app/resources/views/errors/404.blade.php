@extends('layouts.app')
@section('titulo', 'Página no encontrada')
@section('contenido')
    <div class="spa-card text-center" style="padding:3rem 1.5rem">
        <i class="bi bi-search" style="font-size:4rem;color:var(--spa-primary)"></i>
        <h2 class="mt-3" style="color:var(--spa-secondary)">404 — Página no encontrada</h2>
        <p class="text-spa-muted">La página que buscas no existe o fue movida.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-spa-primary">
            <i class="bi bi-house"></i> Ir al Dashboard
        </a>
    </div>
@endsection
