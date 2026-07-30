@extends($layout ?? 'layouts.app')
@section('titulo', 'Reporte de cierre — ' . $caja->fecha->format('d/m/Y'))

@push('styles')
<style>
@media print {
    .spa-sidebar, .spa-topbar, .no-print { display: none !important; }
    .spa-main { margin: 0 !important; }
    .spa-content { padding: 0 !important; }
}
.reporte {
    max-width: 680px; margin: 0 auto;
    background: #fff;
    padding: 2rem;
    border-radius: var(--spa-radius);
    box-shadow: var(--spa-shadow);
    border: 1px solid var(--spa-border);
}
.reporte-logo { display: block; max-height: 70px; max-width: 200px; margin: 0 auto .75rem; object-fit: contain; }
.reporte h2 { color: var(--spa-secondary); text-align: center; margin: 0 0 .25rem; }
.reporte .empresa { text-align: center; color: var(--spa-muted); font-size: .85rem; margin-bottom: .5rem; }
.reporte .subtitulo { text-align: center; font-weight: 700; color: var(--spa-primary-dark); text-transform: uppercase; letter-spacing: 1px; font-size: .85rem; margin-bottom: 1.2rem; }
.reporte .meta { font-size: .9rem; margin-bottom: 1.2rem; padding-bottom: 1rem; border-bottom: 1px dashed var(--spa-border); }
.reporte .meta div { display: flex; justify-content: space-between; padding: 3px 0; }
.reporte-resumen { width: 100%; font-size: .92rem; margin-bottom: 1.2rem; }
.reporte-resumen td { padding: 6px 4px; }
.reporte-resumen tr.total td { border-top: 2px solid var(--spa-border); font-weight: 800; font-size: 1.05rem; color: var(--spa-secondary); padding-top: 10px; }
.reporte-mov { width: 100%; font-size: .85rem; margin-bottom: 1rem; }
.reporte-mov td, .reporte-mov th { padding: 5px 4px; }
.reporte-mov thead th { border-bottom: 1px solid var(--spa-border); text-align: left; font-size: .72rem; text-transform: uppercase; color: var(--spa-secondary); }
.badge-dif { display: inline-block; padding: .3rem .8rem; border-radius: 20px; font-weight: 700; }
</style>
@endpush

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="d-flex justify-content-between mb-3 no-print flex-wrap gap-2">
    @if(! ($publico ?? false))
        <a href="{{ route('caja.show', $caja) }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    @else
        <span></span>
    @endif
    <div class="d-flex gap-2">
        @if(! ($publico ?? false))
            <a href="https://wa.me/?text={{ urlencode('Reporte de cierre de caja del ' . $caja->fecha->format('d/m/Y') . ' (' . ($configEmpresa->nombre_empresa ?? 'Spa') . '): ' . \Illuminate\Support\Facades\URL::signedRoute('publico.caja.reporte', $caja)) }}"
               target="_blank" class="btn" style="background:#25D366;color:#fff">
                <i class="bi bi-whatsapp"></i> Enviar por WhatsApp
            </a>
        @endif
        <button onclick="window.print()" class="btn btn-spa-primary"><i class="bi bi-printer"></i> Imprimir / Guardar PDF</button>
    </div>
</div>

<div class="reporte">
    @if($configEmpresa?->logoUrl())
        <img src="{{ $configEmpresa->logoUrl() }}" alt="Logo" class="reporte-logo">
    @endif
    <h2>{{ $configEmpresa?->nombre_empresa ?? 'Estética & SPA' }}</h2>
    <div class="empresa">
        @if($configEmpresa?->direccion){{ $configEmpresa->direccion }}<br>@endif
        @if($configEmpresa?->telefono){{ $configEmpresa->telefono }}@endif
    </div>
    <div class="subtitulo">Reporte de cierre de caja</div>

    <div class="meta">
        <div><span>Fecha</span><strong>{{ $caja->fecha->locale('es')->isoFormat('D [de] MMMM YYYY') }}</strong></div>
        <div><span>Responsable</span><strong>{{ $caja->usuario->name }}</strong></div>
        <div><span>Apertura</span><strong>{{ $caja->abierta_en->format('H:i') }}</strong></div>
        <div><span>Cierre</span><strong>{{ $caja->cerrada_en?->format('H:i') ?? '— (caja abierta)' }}</strong></div>
    </div>

    <table class="reporte-resumen">
        <tr><td>Monto de apertura</td><td class="text-end">{{ $sim }} {{ number_format($caja->monto_apertura, 2) }}</td></tr>
        <tr><td>+ Ventas en efectivo</td><td class="text-end">{{ $sim }} {{ number_format($totales['ventas_efectivo'], 2) }}</td></tr>
        <tr><td>+ Ingresos</td><td class="text-end">{{ $sim }} {{ number_format($totales['ingresos'], 2) }}</td></tr>
        <tr><td>− Egresos (gastos)</td><td class="text-end">{{ $sim }} {{ number_format($totales['egresos'], 2) }}</td></tr>
        <tr class="total"><td>Efectivo esperado</td><td class="text-end">{{ $sim }} {{ number_format($totales['esperado'], 2) }}</td></tr>
        @if($caja->estado === 'cerrada')
            <tr><td>Efectivo contado</td><td class="text-end">{{ $sim }} {{ number_format($caja->monto_cierre, 2) }}</td></tr>
            <tr>
                <td>Diferencia</td>
                <td class="text-end">
                    @php $dif = (float) $caja->diferencia; @endphp
                    <span class="badge-dif" style="background:{{ $dif == 0 ? '#e0efe3' : ($dif > 0 ? '#d9e6ef' : '#f0d4d4') }};color:{{ $dif == 0 ? '#2e6a3a' : ($dif > 0 ? '#2c5d80' : '#7d2e2e') }}">
                        {{ $dif > 0 ? '+' : '' }}{{ $sim }} {{ number_format($dif, 2) }}
                        @if($dif == 0) (exacto) @elseif($dif > 0) (sobrante) @else (faltante) @endif
                    </span>
                </td>
            </tr>
        @endif
    </table>

    @if($caja->movimientos->isNotEmpty())
        <table class="reporte-mov">
            <thead><tr><th>Hora</th><th>Tipo</th><th>Concepto</th><th class="text-end">Monto</th></tr></thead>
            <tbody>
            @foreach($caja->movimientos->sortBy('created_at') as $m)
                <tr>
                    <td>{{ $m->created_at->format('H:i') }}</td>
                    <td>{{ $m->tipo === 'ingreso' ? 'Ingreso' : 'Gasto' }}</td>
                    <td>{{ $m->concepto }}</td>
                    <td class="text-end">{{ $m->tipo === 'ingreso' ? '+' : '−' }} {{ $sim }} {{ number_format($m->monto, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if($caja->notas_apertura)
        <div class="form-text"><strong>Notas de apertura:</strong> {{ $caja->notas_apertura }}</div>
    @endif
    @if($caja->notas_cierre)
        <div class="form-text"><strong>Notas de cierre:</strong> {{ $caja->notas_cierre }}</div>
    @endif
</div>
@endsection
