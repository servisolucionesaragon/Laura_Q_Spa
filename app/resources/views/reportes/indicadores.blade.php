@extends('layouts.app')
@section('titulo', 'Indicadores')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="spa-welcome" style="margin-bottom:1.5rem">
    <h2 style="color:#fff"><i class="bi bi-bar-chart"></i> Indicadores generales</h2>
    <p style="opacity:.9;margin:0">KPIs del mes en curso ({{ now()->locale('es')->isoFormat('MMMM YYYY') }})</p>
</div>

<div class="row g-3 mb-2">
    <div class="col-md-3"><div class="spa-stat"><div class="icon"><i class="bi bi-people"></i></div><div class="label">Clientes totales</div><div class="value">{{ $kpis['clientes_total'] }}</div><div class="meta">{{ $kpis['clientes_activos'] }} activos</div></div></div>
    <div class="col-md-3"><div class="spa-stat info"><div class="icon"><i class="bi bi-person-plus"></i></div><div class="label">Nuevos del mes</div><div class="value">{{ $kpis['clientes_mes'] }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat success"><div class="icon"><i class="bi bi-cash-coin"></i></div><div class="label">Ventas del mes</div><div class="value">{{ $sim }} {{ number_format($kpis['ventas_mes'], 2) }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat warning"><div class="icon"><i class="bi bi-calendar-check"></i></div><div class="label">Citas del mes</div><div class="value">{{ $kpis['citas_mes'] }}</div><div class="meta">{{ $kpis['citas_realizadas'] }} realizadas</div></div></div>
</div>

<div class="row g-3 mb-2">
    <div class="col-md-3"><div class="spa-stat"><div class="icon"><i class="bi bi-box-seam"></i></div><div class="label">Productos activos</div><div class="value">{{ $kpis['productos_total'] }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat danger"><div class="icon"><i class="bi bi-exclamation-triangle"></i></div><div class="label">Stock bajo</div><div class="value">{{ $kpis['productos_bajo'] }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat danger"><div class="icon"><i class="bi bi-x-circle"></i></div><div class="label">Citas canceladas</div><div class="value">{{ $kpis['citas_canceladas'] }}</div></div></div>
    <div class="col-md-3"><div class="spa-stat success"><div class="icon"><i class="bi bi-check-circle"></i></div><div class="label">% Realización</div>
        @php $tot = $kpis['citas_mes'] ?: 1; @endphp
        <div class="value">{{ round(($kpis['citas_realizadas'] / $tot) * 100) }}%</div>
    </div></div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-7">
        <div class="spa-card">
            <h4 style="color:var(--spa-secondary)"><i class="bi bi-graph-up"></i> Ventas últimos 6 meses</h4>
            <div style="height:300px"><canvas id="cMes"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-xl-5">
        <div class="spa-card">
            <h4 style="color:var(--spa-secondary)"><i class="bi bi-exclamation-triangle text-spa-warning"></i> Productos con stock bajo</h4>
            @if($stockBajo->isEmpty())
                <div class="alert alert-success"><i class="bi bi-check-circle"></i><div>¡Todos los productos están con stock adecuado!</div></div>
            @else
                <table class="spa-table">
                    <thead><tr><th>Producto</th><th>Stock</th><th>Mín</th></tr></thead>
                    <tbody>
                    @foreach($stockBajo as $p)
                        <tr>
                            <td>{{ $p->nombre }}<br><small class="text-spa-muted">{{ $p->categoria?->nombre }}</small></td>
                            <td><strong style="color:{{ $p->stock_actual <= 0 ? 'var(--spa-danger)' : 'var(--spa-warning)' }}">{{ $p->stock_actual }}</strong></td>
                            <td>{{ $p->stock_minimo }}</td>
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

const ctx = document.getElementById('cMes');
if (ctx) {
    const grad = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(163, 88, 128, .45)');
    grad.addColorStop(1, 'rgba(163, 88, 128, .03)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($ventasMes->pluck('mes')),
            datasets: [{
                label: 'Ventas',
                data: @json($ventasMes->pluck('total')),
                borderColor: '#a35880', backgroundColor: grad,
                borderWidth: 3, fill: true, tension: 0.36,
                pointRadius: 5, pointBackgroundColor: '#fff', pointBorderColor: '#7a3d5e', pointBorderWidth: 2,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { backgroundColor:'rgba(46,28,51,.95)', titleColor:'#fff', bodyColor:'#fff' } }, scales: { y: { beginAtZero: true } } }
    });
}
</script>
@endpush
