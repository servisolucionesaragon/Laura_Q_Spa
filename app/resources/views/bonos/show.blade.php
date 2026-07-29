@extends('layouts.app')
@section('titulo', 'Bono ' . $bono->codigo)

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; $pct = $bono->sesiones_total > 0 ? ($bono->sesiones_usadas / $bono->sesiones_total) * 100 : 0; @endphp

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="spa-card" style="background:linear-gradient(135deg, var(--spa-primary-dark), var(--spa-secondary));color:#fff;border:none;text-align:center">
            <div style="font-size:.78rem;opacity:.8;letter-spacing:1.5px">CÓDIGO</div>
            <div style="font-family:'Courier New',monospace;font-size:1.6rem;font-weight:700;letter-spacing:3px;margin:.4rem 0">{{ $bono->codigo }}</div>
            <h3 style="color:#fff;margin:.5rem 0">{{ $bono->nombre }}</h3>
            <div style="font-size:2.4rem;font-weight:800;margin:.8rem 0 .2rem">{{ $bono->sesiones_usadas }}/{{ $bono->sesiones_total }}</div>
            <div style="opacity:.85">sesiones usadas</div>
            <div style="height:8px;background:rgba(255,255,255,.18);border-radius:4px;margin:.85rem 0">
                <div style="height:100%;width:{{ $pct }}%;background:var(--spa-accent);border-radius:4px"></div>
            </div>
            <hr style="border-color:rgba(255,255,255,.18);margin:1rem 0">
            <div class="d-flex justify-content-between" style="font-size:.85rem">
                <div><div style="opacity:.7">Cliente</div><strong>{{ $bono->cliente?->nombre_completo }}</strong></div>
                <div style="text-align:right"><div style="opacity:.7">Estado</div><strong>{{ ucfirst($bono->estado) }}</strong></div>
            </div>
            <div class="d-flex justify-content-between mt-2" style="font-size:.85rem">
                <div><div style="opacity:.7">Compra</div><strong>{{ $bono->fecha_compra->format('d/m/Y') }}</strong></div>
                <div style="text-align:right"><div style="opacity:.7">Vence</div><strong>{{ $bono->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</strong></div>
            </div>
        </div>

        <a href="{{ route('bonos.index') }}" class="btn btn-spa-secondary btn-block"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>

    <div class="col-12 col-lg-7">
        <div class="spa-card">
            <h5 style="color:var(--spa-secondary)"><i class="bi bi-plus-circle"></i> Registrar consumo de sesión</h5>
            @if($bono->sesiones_restantes <= 0)
                <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i><div>Este bono ya no tiene sesiones disponibles.</div></div>
            @else
                <form method="POST" action="{{ route('bonos.consumir', $bono) }}" class="row g-3">
                    @csrf
                    <div class="col-md-4"><label class="form-label">Fecha *</label><input type="date" name="fecha" class="form-control" required value="{{ now()->format('Y-m-d') }}"></div>
                    <div class="col-md-8"><label class="form-label">Descripción</label><input type="text" name="descripcion" class="form-control" placeholder="Sesión {{ $bono->sesiones_usadas + 1 }}"></div>
                    <div class="col-12"><button class="btn btn-spa-primary"><i class="bi bi-check-circle"></i> Registrar consumo</button></div>
                </form>
            @endif
        </div>

        <div class="spa-card">
            <h5 style="color:var(--spa-secondary)"><i class="bi bi-clock-history"></i> Historial de consumos</h5>
            @if($bono->consumos->isEmpty())
                <p class="text-spa-muted">Sin consumos.</p>
            @else
                <table class="spa-table">
                    <thead><tr><th>Fecha</th><th>Descripción</th><th>Registrado por</th></tr></thead>
                    <tbody>
                    @foreach($bono->consumos as $c)
                        <tr><td>{{ $c->fecha->format('d/m/Y') }}</td><td>{{ $c->descripcion }}</td><td><small>{{ $c->user?->name ?? '—' }}</small></td></tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
