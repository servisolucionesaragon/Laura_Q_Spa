@extends('layouts.app')
@section('titulo', 'Caja del ' . $caja->fecha->format('d/m/Y'))

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp
@include('layouts.partials.errors')

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-cash-stack text-spa-primary"></i> Caja del {{ $caja->fecha->format('d/m/Y') }}</h3>
            <small class="text-spa-muted">
                Abierta por {{ $caja->usuario->name }} el {{ $caja->abierta_en->format('d/m/Y H:i') }}
                @if($caja->estado === 'cerrada') · Cerrada el {{ $caja->cerrada_en->format('d/m/Y H:i') }} @endif
            </small>
        </div>
        <div class="d-flex gap-2">
            <span class="spa-badge {{ $caja->estado === 'abierta' ? 'success' : '' }}" style="{{ $caja->estado === 'cerrada' ? 'background:var(--spa-secondary);color:#fff' : '' }}">
                {{ $caja->estado === 'abierta' ? 'Abierta' : 'Cerrada' }}
            </span>
            <a href="{{ route('caja.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div class="spa-stat" style="padding:1rem">
                <div class="label">Apertura</div>
                <div class="value" style="font-size:1.4rem">{{ $sim }} {{ number_format($caja->monto_apertura, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="spa-stat success" style="padding:1rem">
                <div class="label">Ventas efectivo</div>
                <div class="value" style="font-size:1.4rem">{{ $sim }} {{ number_format($totales['ventas_efectivo'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="spa-stat info" style="padding:1rem">
                <div class="label">Ingresos</div>
                <div class="value" style="font-size:1.4rem">{{ $sim }} {{ number_format($totales['ingresos'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="spa-stat warning" style="padding:1rem">
                <div class="label">Egresos</div>
                <div class="value" style="font-size:1.4rem">{{ $sim }} {{ number_format($totales['egresos'], 2) }}</div>
            </div>
        </div>
    </div>

    <hr style="border-color:var(--spa-border-soft)">

    <div class="row g-3">
        <div class="col-md-6">
            <div style="background:var(--spa-bg-light);border-radius:var(--spa-radius-sm);padding:1rem 1.2rem">
                <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.1rem;color:var(--spa-secondary)">
                    <span>Efectivo esperado en caja</span>
                    <span>{{ $sim }} {{ number_format($totales['esperado'], 2) }}</span>
                </div>
                <div class="form-text mt-1">Apertura + ventas en efectivo + ingresos − egresos.</div>
            </div>
        </div>

        @if($caja->estado === 'cerrada')
            <div class="col-md-6">
                @php $dif = (float) $caja->diferencia; @endphp
                <div style="background:{{ $dif == 0 ? '#e0efe3' : ($dif > 0 ? '#d9e6ef' : '#f0d4d4') }};border-radius:var(--spa-radius-sm);padding:1rem 1.2rem">
                    <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.1rem;color:{{ $dif == 0 ? '#2e6a3a' : ($dif > 0 ? '#2c5d80' : '#7d2e2e') }}">
                        <span>Contado al cerrar</span>
                        <span>{{ $sim }} {{ number_format($caja->monto_cierre, 2) }}</span>
                    </div>
                    <div class="form-text mt-1">
                        Diferencia: <strong>{{ $dif > 0 ? '+' : '' }}{{ $sim }} {{ number_format($dif, 2) }}</strong>
                        @if($dif == 0) (cuadró exacto)
                        @elseif($dif > 0) (sobrante)
                        @else (faltante) @endif
                    </div>
                    @if($caja->notas_cierre)
                        <div class="form-text mt-1"><i class="bi bi-journal-text"></i> {{ $caja->notas_cierre }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@if($caja->estado === 'abierta')
    <div class="spa-card">
        <div class="spa-card-header">
            <h4 style="margin:0"><i class="bi bi-plus-circle text-spa-primary"></i> Registrar gasto o ingreso</h4>
        </div>
        <form method="POST" action="{{ route('caja.movimientos.store', $caja) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-2">
                <label class="form-label">Tipo *</label>
                <select name="tipo" class="form-select" required>
                    <option value="egreso">Gasto</option>
                    <option value="ingreso">Ingreso</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Concepto *</label>
                <input type="text" name="concepto" class="form-control" placeholder="Ej. Compra de insumos, pago a domicilio..." required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Monto *</label>
                <input type="number" step="0.01" min="0.01" name="monto" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-spa-primary btn-block"><i class="bi bi-plus-lg"></i> Registrar</button>
            </div>
        </form>
    </div>
@endif

<div class="spa-card">
    <div class="spa-card-header">
        <h4 style="margin:0"><i class="bi bi-list-ul text-spa-primary"></i> Movimientos de caja</h4>
    </div>

    @if($caja->movimientos->isEmpty())
        <div class="text-center py-3 text-spa-muted"><p>Sin gastos ni ingresos registrados.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Hora</th><th>Tipo</th><th>Concepto</th><th class="text-end">Monto</th><th>Registrado por</th>@if($caja->estado === 'abierta')<th class="text-end">Acciones</th>@endif</tr></thead>
                <tbody>
                @foreach($caja->movimientos as $m)
                    <tr>
                        <td>{{ $m->created_at->format('H:i') }}</td>
                        <td><span class="spa-badge {{ $m->tipo === 'ingreso' ? 'success' : 'danger' }}">{{ $m->tipo === 'ingreso' ? 'Ingreso' : 'Gasto' }}</span></td>
                        <td>{{ $m->concepto }}</td>
                        <td class="text-end"><strong>{{ $m->tipo === 'ingreso' ? '+' : '−' }} {{ $sim }} {{ number_format($m->monto, 2) }}</strong></td>
                        <td><small class="text-spa-muted">{{ $m->usuario->name }}</small></td>
                        @if($caja->estado === 'abierta')
                            <td class="text-end">
                                <form action="{{ route('caja.movimientos.destroy', [$caja, $m]) }}" method="POST" onsubmit="return confirm('¿Eliminar este movimiento?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@if($caja->estado === 'abierta')
    <div class="spa-card">
        <div class="spa-card-header">
            <h4 style="margin:0"><i class="bi bi-lock text-spa-primary"></i> Cerrar caja</h4>
        </div>
        <form method="POST" action="{{ route('caja.cerrar', $caja) }}" onsubmit="return confirm('¿Confirmas el cierre de caja? Esta acción no se puede deshacer.')">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Efectivo contado en caja *</label>
                    <input type="number" step="0.01" min="0" name="monto_cierre" class="form-control" required>
                    <div class="form-text">Cuenta el efectivo físico y escribe el total real aquí.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Notas de cierre</label>
                    <textarea name="notas_cierre" class="form-control" rows="1"></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button class="btn" style="background:var(--spa-danger);color:#fff"><i class="bi bi-lock-fill"></i> Cerrar caja</button>
            </div>
        </form>
    </div>
@endif
@endsection
