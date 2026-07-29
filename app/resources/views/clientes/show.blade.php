@extends('layouts.app')
@section('titulo', $cliente->nombre_completo)

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="row g-3">
    {{-- Tarjeta de cliente --}}
    <div class="col-12 col-lg-4">
        <div class="spa-card text-center" style="background:linear-gradient(135deg, var(--spa-secondary), var(--spa-primary-dark));color:#fff;border:none">
            <div style="width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.18);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:2.2rem;margin:0 auto 1rem">
                {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
            </div>
            <h3 style="color:#fff;margin:0 0 .25rem">{{ $cliente->nombre_completo }}</h3>
            @if($cliente->fecha_nacimiento)<div style="opacity:.85;font-size:.9rem">{{ $cliente->edad }} años · {{ $cliente->fecha_nacimiento->locale('es')->isoFormat('D MMM YYYY') }}</div>@endif
            <div style="margin-top:1rem">
                @if($cliente->activo)<span class="spa-badge success">Activo</span>@else<span class="spa-badge danger">Inactivo</span>@endif
                @if($cliente->acepta_marketing)<span class="spa-badge info">Acepta promos</span>@endif
            </div>
            <hr style="border-color:rgba(255,255,255,.2);margin:1.2rem 0">
            <div style="text-align:left;font-size:.92rem;display:flex;flex-direction:column;gap:.5rem">
                @if($cliente->telefono)<div><i class="bi bi-telephone"></i> {{ $cliente->telefono }}</div>@endif
                @if($cliente->email)<div><i class="bi bi-envelope"></i> {{ $cliente->email }}</div>@endif
                @if($cliente->direccion)<div><i class="bi bi-geo-alt"></i> {{ $cliente->direccion }}{{ $cliente->ciudad ? ', '.$cliente->ciudad : '' }}</div>@endif
                @if($cliente->documento)<div><i class="bi bi-credit-card-2-front"></i> {{ $cliente->documento }}</div>@endif
            </div>
        </div>

        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-spa-primary btn-block"><i class="bi bi-pencil"></i> Editar</a>
            <a href="{{ route('clientes.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        @if($cliente->alergias)
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill"></i><div><strong>Alergias / Contraindicaciones</strong><br>{{ $cliente->alergias }}</div></div>
        @endif

        @if($cliente->notas)
            <div class="spa-card"><h5 style="color:var(--spa-secondary)"><i class="bi bi-journal-text"></i> Notas</h5><p style="margin:0;color:var(--spa-muted)">{{ $cliente->notas }}</p></div>
        @endif
    </div>

    {{-- Historial --}}
    <div class="col-12 col-lg-8">
        <div class="row g-3 mb-2">
            <div class="col-md-4"><div class="spa-stat"><div class="icon"><i class="bi bi-calendar-event"></i></div><div class="label">Citas</div><div class="value">{{ $cliente->citas->count() }}</div></div></div>
            <div class="col-md-4"><div class="spa-stat success"><div class="icon"><i class="bi bi-cash-coin"></i></div><div class="label">Total gastado</div><div class="value">{{ $sim }} {{ number_format($cliente->ventas->sum('total'), 2) }}</div></div></div>
            <div class="col-md-4"><div class="spa-stat warning"><div class="icon"><i class="bi bi-gift"></i></div><div class="label">Bonos</div><div class="value">{{ $cliente->bonos->count() }}</div></div></div>
        </div>

        <div class="spa-card">
            <h5 style="color:var(--spa-secondary)"><i class="bi bi-calendar-week"></i> Historial de citas</h5>
            @if($cliente->citas->isEmpty())
                <p class="text-spa-muted">Sin citas registradas.</p>
            @else
                <table class="spa-table">
                    <thead><tr><th>Fecha</th><th>Servicio</th><th>Profesional</th><th>Estado</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach($cliente->citas->take(20) as $c)
                        <tr>
                            <td>{{ $c->fecha->format('d/m/Y') }} <small class="text-spa-muted">{{ \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') }}</small></td>
                            <td>{{ $c->servicios->pluck('descripcion')->join(', ') ?: '—' }}</td>
                            <td><small>{{ $c->profesional?->name ?? '—' }}</small></td>
                            <td><span class="spa-badge {{ $c->estado_badge }}">{{ $c->estado_label }}</span></td>
                            <td><strong>{{ $sim }} {{ number_format($c->total, 2) }}</strong></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="spa-card">
            <h5 style="color:var(--spa-secondary)"><i class="bi bi-gift"></i> Bonos</h5>
            @if($cliente->bonos->isEmpty())
                <p class="text-spa-muted">Sin bonos.</p>
            @else
                <table class="spa-table">
                    <thead><tr><th>Código</th><th>Bono</th><th>Sesiones</th><th>Vence</th><th>Estado</th></tr></thead>
                    <tbody>
                    @foreach($cliente->bonos as $b)
                        <tr>
                            <td><code>{{ $b->codigo }}</code></td>
                            <td>{{ $b->nombre }}</td>
                            <td>{{ $b->sesiones_usadas }} / {{ $b->sesiones_total }}</td>
                            <td>{{ $b->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                            <td><span class="spa-badge {{ $b->estado === 'activo' ? 'success' : ($b->estado === 'agotado' ? 'warning' : 'danger') }}">{{ ucfirst($b->estado) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
