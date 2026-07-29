@extends('layouts.app')
@section('titulo', 'Reporte de ventas')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<form method="GET" class="spa-card">
    <div class="row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label">Desde</label><input type="date" name="desde" class="form-control" value="{{ $desde }}"></div>
        <div class="col-md-4"><label class="form-label">Hasta</label><input type="date" name="hasta" class="form-control" value="{{ $hasta }}"></div>
        <div class="col-md-4"><button class="btn btn-spa-primary btn-block"><i class="bi bi-arrow-clockwise"></i> Actualizar</button></div>
    </div>
</form>

<div class="row g-3 mb-2">
    <div class="col-md-3"><div class="spa-stat success"><div class="icon"><i class="bi bi-cash-coin"></i></div><div class="label">Total facturado</div><div class="value">{{ $sim }} {{ number_format($resumen['total'], 2) }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat info"><div class="icon"><i class="bi bi-receipt"></i></div><div class="label">Ventas</div><div class="value">{{ $resumen['cantidad'] }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat"><div class="icon"><i class="bi bi-graph-up"></i></div><div class="label">Ticket promedio</div><div class="value">{{ $sim }} {{ number_format($resumen['ticket_promedio'], 2) }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat warning"><div class="icon"><i class="bi bi-tag"></i></div><div class="label">Descuentos</div><div class="value">{{ $sim }} {{ number_format($resumen['descuentos'], 2) }}</div></div></div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="spa-card">
            <h4 style="color:var(--spa-secondary)"><i class="bi bi-bar-chart"></i> Ventas por día</h4>
            @if($porDia->isEmpty())
                <p class="text-spa-muted">Sin ventas en este período.</p>
            @else
                <div style="height:280px"><canvas id="cVentasDia"></canvas></div>
            @endif
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="spa-card">
            <h4 style="color:var(--spa-secondary)"><i class="bi bi-credit-card"></i> Por método de pago</h4>
            @if($porMetodo->isEmpty())
                <p class="text-spa-muted">Sin datos.</p>
            @else
                <div style="height:280px"><canvas id="cMetodos"></canvas></div>
            @endif
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="spa-card">
            <h4 style="color:var(--spa-secondary)"><i class="bi bi-flower2"></i> Top servicios</h4>
            @if($topServicios->isEmpty())<p class="text-spa-muted">Sin datos.</p>@else
                <table class="spa-table">
                    <thead><tr><th>Servicio</th><th>Cantidad</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @foreach($topServicios as $s)
                        <tr><td>{{ $s->descripcion }}</td><td>{{ rtrim(rtrim($s->cantidad, '0'), '.') }}</td><td class="text-end"><strong>{{ $sim }} {{ number_format($s->total, 2) }}</strong></td></tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="spa-card">
            <h4 style="color:var(--spa-secondary)"><i class="bi bi-box-seam"></i> Top productos</h4>
            @if($topProductos->isEmpty())<p class="text-spa-muted">Sin datos.</p>@else
                <table class="spa-table">
                    <thead><tr><th>Producto</th><th>Cant.</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @foreach($topProductos as $p)
                        <tr><td>{{ $p->descripcion }}</td><td>{{ rtrim(rtrim($p->cantidad, '0'), '.') }}</td><td class="text-end"><strong>{{ $sim }} {{ number_format($p->total, 2) }}</strong></td></tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="col-12">
        <div class="spa-card">
            <h4 style="color:var(--spa-secondary)"><i class="bi bi-trophy"></i> Top profesionales (por citas realizadas)</h4>
            @if($topProfesionales->isEmpty())<p class="text-spa-muted">Sin datos.</p>@else
                <table class="spa-table">
                    <thead><tr><th>#</th><th>Profesional</th><th>Citas</th><th class="text-end">Ingresos generados</th></tr></thead>
                    <tbody>
                    @foreach($topProfesionales as $i => $p)
                        <tr>
                            <td><strong>{{ $i + 1 }}</strong></td>
                            <td>{{ $p->profesional?->name ?? '—' }}</td>
                            <td>{{ $p->citas }}</td>
                            <td class="text-end"><strong>{{ $sim }} {{ number_format($p->total, 2) }}</strong></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Poppins', sans-serif";
Chart.defaults.color = '#5d4858';

const tooltipCommon = { backgroundColor: 'rgba(46,28,51,.95)', titleColor:'#fff', bodyColor:'#fff', cornerRadius: 8, padding: 10 };

@if(! $porDia->isEmpty())
new Chart(document.getElementById('cVentasDia'), {
    type: 'bar',
    data: {
        labels: @json($porDia->pluck('dia')),
        datasets: [{ label: 'Ventas', data: @json($porDia->pluck('total')), backgroundColor: '#a35880', borderRadius: 8 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: tooltipCommon }, scales: { y: { beginAtZero: true } } }
});
@endif

@if(! $porMetodo->isEmpty())
new Chart(document.getElementById('cMetodos'), {
    type: 'doughnut',
    data: {
        labels: @json($porMetodo->pluck('metodo_pago')),
        datasets: [{ data: @json($porMetodo->pluck('total')), backgroundColor: ['#4d8b58','#487da0','#a87f48','#a35880','#5d4858'], borderColor:'#fff', borderWidth: 3 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: tooltipCommon } }
});
@endif
</script>
@endpush
