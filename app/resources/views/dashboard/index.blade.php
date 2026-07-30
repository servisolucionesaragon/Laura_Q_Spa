@extends('layouts.app')

@section('titulo', 'Dashboard')

@push('styles')
<style>
    .chart-card {
        background: var(--spa-surface);
        border: 1px solid var(--spa-border);
        border-radius: var(--spa-radius);
        box-shadow: var(--spa-shadow);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        height: 100%;
    }
    .chart-card .chart-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1rem;
    }
    .chart-card .chart-title {
        font-weight: 700;
        color: var(--spa-secondary);
        margin: 0;
        font-size: 1.05rem;
        display: flex; align-items: center; gap: .5rem;
    }
    .chart-card .chart-subtitle {
        font-size: .82rem;
        color: var(--spa-muted);
        margin: 2px 0 0;
    }
    .chart-container {
        position: relative;
        height: 280px;
    }
    .chart-container.tall { height: 320px; }

    .ranking-row {
        display: flex; align-items: center; gap: .8rem;
        padding: .65rem .25rem;
        border-bottom: 1px solid var(--spa-border-soft);
    }
    .ranking-row:last-child { border-bottom: none; }
    .ranking-row .pos {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: var(--spa-secondary);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        font-size: .85rem;
        flex-shrink: 0;
    }
    .ranking-row .pos.gold   { background: linear-gradient(135deg, #d4a857, #a87f48); box-shadow: 0 3px 10px rgba(168, 127, 72, .4); }
    .ranking-row .pos.silver { background: linear-gradient(135deg, #c0c0c0, #909090); box-shadow: 0 3px 10px rgba(144, 144, 144, .4); }
    .ranking-row .pos.bronze { background: linear-gradient(135deg, #cd7f32, #8b5a23); box-shadow: 0 3px 10px rgba(139, 90, 35, .4); }
    .ranking-row .info { flex: 1; min-width: 0; }
    .ranking-row .info .name { font-weight: 600; color: var(--spa-secondary); }
    .ranking-row .info .meta { font-size: .8rem; color: var(--spa-muted); }
    .ranking-row .value { font-weight: 700; color: var(--spa-primary-dark); }

    .cita-row {
        display: flex; gap: .85rem;
        padding: .8rem .85rem;
        border: 1px solid var(--spa-border-soft);
        border-radius: var(--spa-radius-sm);
        margin-bottom: .55rem;
        background: var(--spa-surface);
        transition: all .18s ease;
    }
    .cita-row:hover { border-color: var(--spa-primary); transform: translateX(2px); }
    .cita-row .hora-block {
        background: linear-gradient(135deg, var(--spa-primary), var(--spa-primary-dark));
        color: #fff;
        border-radius: 8px;
        padding: .35rem .6rem;
        text-align: center;
        flex-shrink: 0;
        min-width: 64px;
    }
    .cita-row .hora-block .h { font-weight: 700; font-size: 1rem; line-height: 1; }
    .cita-row .hora-block .d { font-size: .68rem; opacity: .9; margin-top: 2px; }
    .cita-row .info { flex: 1; min-width: 0; }
    .cita-row .info .cliente { font-weight: 600; color: var(--spa-secondary); }
    .cita-row .info .meta {
        font-size: .8rem; color: var(--spa-muted);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
</style>
@endpush

@section('contenido')
    @php
        $simbolo = $configEmpresa?->simbolo_moneda ?? 'Q';
        $hora    = now()->format('H');
        $saludo  = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    @endphp

    {{-- Bienvenida --}}
    <div class="spa-welcome">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 style="margin:0;font-weight:600">
                    {{ $saludo }}, {{ explode(' ', auth()->user()->name)[0] }} <span style="font-size:1.4rem">✨</span>
                </h2>
                <p style="margin:.25rem 0 0">
                    Hoy es {{ \Carbon\Carbon::parse($hoy)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.
                    Aquí tienes el resumen de tu centro.
                </p>
            </div>
            <div style="position:relative;z-index:2;display:flex;gap:.5rem;flex-wrap:wrap">
                <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);padding:.5rem 1rem;border-radius:30px;color:#fff;font-size:.9rem">
                    <i class="bi bi-cash-stack"></i> Mes: <strong>{{ $simbolo }} {{ number_format($stats['ventas_mes'], 2) }}</strong>
                </div>
                <a href="{{ route('citas.create') }}" class="btn" style="background:#fff;color:var(--spa-secondary);font-weight:600">
                    <i class="bi bi-calendar-plus"></i> Nueva cita
                </a>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-2">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="spa-stat">
                <div class="icon"><i class="bi bi-calendar-event"></i></div>
                <div class="label">Citas de hoy</div>
                <div class="value">{{ $stats['citas_hoy'] }}</div>
                <div class="meta"><i class="bi bi-clock"></i> Programadas</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="spa-stat success">
                <div class="icon"><i class="bi bi-cash-coin"></i></div>
                <div class="label">Ventas de hoy</div>
                <div class="value">{{ $simbolo }} {{ number_format($stats['ventas_hoy'], 2) }}</div>
                <div class="meta"><i class="bi bi-arrow-up-right"></i> Total facturado</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="spa-stat info">
                <div class="icon"><i class="bi bi-person-plus"></i></div>
                <div class="label">Clientes nuevos</div>
                <div class="value">{{ $stats['clientes_nuevos'] }}</div>
                <div class="meta"><i class="bi bi-calendar"></i> Esta semana</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="spa-stat warning">
                <div class="icon"><i class="bi bi-gift"></i></div>
                <div class="label">Bonos activos</div>
                <div class="value">{{ $stats['bonos_activos'] }}</div>
                <div class="meta"><i class="bi bi-stars"></i> Por consumir</div>
            </div>
        </div>
    </div>

    {{-- Gráficos principales --}}
    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title"><i class="bi bi-graph-up-arrow text-spa-primary"></i> Evolución de ventas</h3>
                        <p class="chart-subtitle">Total facturado en los últimos 14 días</p>
                    </div>
                    <span class="spa-badge dark"><i class="bi bi-calendar-range"></i> 14 días</span>
                </div>
                <div class="chart-container tall">
                    <canvas id="chartVentas"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title"><i class="bi bi-pie-chart-fill text-spa-primary"></i> Estado de citas</h3>
                        <p class="chart-subtitle">Distribución últimos 45 días</p>
                    </div>
                </div>
                <div class="chart-container tall">
                    <canvas id="chartEstadoCitas"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="spa-card">
        <div class="spa-card-header">
            <h3>Accesos rápidos</h3>
            <span class="text-spa-muted" style="font-size:.85rem">Acciones más frecuentes</span>
        </div>
        <div class="row g-3">
            @foreach($accesosRapidos as $accion)
                <div class="col-6 col-md-3">
                    <a href="{{ $accion['ruta'] }}" class="spa-quick-action {{ $accion['color'] }}">
                        <div class="ic"><i class="bi bi-{{ $accion['icono'] }}"></i></div>
                        <div>{{ $accion['titulo'] }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Segunda fila gráficos --}}
    <div class="row g-3">
        <div class="col-12 col-xl-7">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title"><i class="bi bi-bar-chart-fill text-spa-primary"></i> Top servicios</h3>
                        <p class="chart-subtitle">Servicios más realizados (últimos 30 días)</p>
                    </div>
                </div>
                @if($topServicios->isEmpty())
                    <div class="text-center py-4" style="color:var(--spa-muted)">
                        <i class="bi bi-bar-chart" style="font-size:2.5rem;opacity:.4"></i>
                        <p style="margin-top:.7rem">Aún no hay datos suficientes.</p>
                    </div>
                @else
                    <div class="chart-container tall">
                        <canvas id="chartTopServicios"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title"><i class="bi bi-credit-card-fill text-spa-primary"></i> Métodos de pago</h3>
                        <p class="chart-subtitle">Ventas del mes actual</p>
                    </div>
                </div>
                @if($ventasPorMetodo->isEmpty())
                    <div class="text-center py-4" style="color:var(--spa-muted)">
                        <i class="bi bi-credit-card" style="font-size:2.5rem;opacity:.4"></i>
                        <p style="margin-top:.7rem">Sin ventas registradas este mes.</p>
                    </div>
                @else
                    <div class="chart-container tall">
                        <canvas id="chartMetodosPago"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Próximas citas + Top profesionales + equipo --}}
    <div class="row g-3">
        <div class="col-12 col-xl-7">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title"><i class="bi bi-calendar-week-fill text-spa-primary"></i> Próximas citas</h3>
                        <p class="chart-subtitle">{{ $proximasCitas->count() }} citas pendientes y confirmadas</p>
                    </div>
                    <a href="{{ route('citas.index') }}" style="font-size:.85rem;font-weight:600">Ver agenda →</a>
                </div>

                @if($proximasCitas->isEmpty())
                    <div class="text-center py-4" style="color:var(--spa-muted)">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem;opacity:.4"></i>
                        <p style="margin-top:.7rem">No hay citas próximas.</p>
                    </div>
                @else
                    @foreach($proximasCitas as $cita)
                        <div class="cita-row">
                            <div class="hora-block">
                                <div class="h">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }}</div>
                                <div class="d">
                                    @if($cita->fecha->isToday())     HOY
                                    @elseif($cita->fecha->isTomorrow()) MAÑ
                                    @else                              {{ $cita->fecha->format('d/m') }}
                                    @endif
                                </div>
                            </div>
                            <div class="info">
                                <div class="cliente">
                                    {{ $cita->cliente->nombre_completo ?? 'Cliente' }}
                                    <span class="spa-badge {{ $cita->estado_badge }}" style="margin-left:.25rem;font-size:.7rem">
                                        {{ $cita->estado_label }}
                                    </span>
                                </div>
                                <div class="meta">
                                    <i class="bi bi-flower2"></i>
                                    {{ $cita->servicios->pluck('descripcion')->join(', ') ?: 'Servicio' }}
                                    @if($cita->profesional)
                                        · <i class="bi bi-person"></i> {{ explode(' ', $cita->profesional->name)[0] }}
                                    @endif
                                    @if($cita->cabina)
                                        · <i class="bi bi-door-open"></i> {{ $cita->cabina->nombre }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title"><i class="bi bi-trophy-fill text-spa-primary"></i> Top profesionales</h3>
                        <p class="chart-subtitle">Citas realizadas (últimos 30 días)</p>
                    </div>
                </div>

                @if($topProfesionales->isEmpty())
                    <div class="text-center py-4" style="color:var(--spa-muted)">
                        <i class="bi bi-people" style="font-size:2.5rem;opacity:.4"></i>
                        <p style="margin-top:.7rem">Aún no hay datos suficientes.</p>
                    </div>
                @else
                    @foreach($topProfesionales as $idx => $prof)
                        <div class="ranking-row">
                            <div class="pos {{ $idx === 0 ? 'gold' : ($idx === 1 ? 'silver' : ($idx === 2 ? 'bronze' : '')) }}">
                                {{ $idx + 1 }}
                            </div>
                            <div class="info">
                                <div class="name">{{ $prof->profesional?->name ?? 'Profesional' }}</div>
                                <div class="meta">
                                    {{ $prof->total_citas }} {{ $prof->total_citas === 1 ? 'cita' : 'citas' }}
                                    · {{ $simbolo }} {{ number_format((float) $prof->ingresos, 2) }}
                                </div>
                            </div>
                            <div class="value">{{ $prof->total_citas }}</div>
                        </div>
                    @endforeach
                @endif

                {{-- Resumen del equipo --}}
                <hr style="border-color:var(--spa-border-soft);margin:1rem 0">
                <div class="d-flex justify-content-between align-items-center" style="padding:.25rem 0">
                    <div>
                        <div style="color:var(--spa-muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.5px">Profesionales activos</div>
                        <div style="font-size:1.4rem;font-weight:700;color:var(--spa-secondary)">{{ $stats['profesionales'] }}</div>
                    </div>
                    <div>
                        <div style="color:var(--spa-muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;text-align:right">Stock bajo</div>
                        <div style="font-size:1.4rem;font-weight:700;color:{{ $stats['productos_bajo'] > 0 ? 'var(--spa-danger)' : 'var(--spa-secondary)' }};text-align:right">
                            {{ $stats['productos_bajo'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    const COL_PRIMARY = '#a35880';
    const COL_PRIMARY_DARK = '#7a3d5e';
    const COL_SECONDARY = '#4a3050';
    const COL_ACCENT = '#a87f48';
    const COL_MUTED = '#5d4858';
    const COL_BORDER = '#c4a8b6';
    const COL_TEXT = '#1f1024';

    Chart.defaults.font.family = "'Poppins', 'Segoe UI', sans-serif";
    Chart.defaults.color = COL_MUTED;
    Chart.defaults.borderColor = COL_BORDER;

    const tooltipCommon = {
        backgroundColor: 'rgba(46, 28, 51, .95)',
        titleColor: '#fff',
        bodyColor: '#fff',
        padding: 12,
        cornerRadius: 8,
        boxPadding: 6,
        titleFont: { weight: '600', size: 13 },
        bodyFont: { size: 12 },
        displayColors: true,
        usePointStyle: true,
        borderColor: COL_PRIMARY,
        borderWidth: 1,
    };

    /* ============== 1. LÍNEA: Ventas últimos 14 días ============== */
    const ventasData = @json($ventasPorDia);
    const ctxV = document.getElementById('chartVentas')?.getContext('2d');
    if (ctxV) {
        const grad = ctxV.createLinearGradient(0, 0, 0, 280);
        grad.addColorStop(0, 'rgba(163, 88, 128, 0.45)');
        grad.addColorStop(1, 'rgba(163, 88, 128, 0.02)');

        new Chart(ctxV, {
            type: 'line',
            data: {
                labels: ventasData.map(d => d.label),
                datasets: [{
                    label: 'Ventas',
                    data: ventasData.map(d => d.total),
                    borderColor: COL_PRIMARY,
                    backgroundColor: grad,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.36,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: COL_PRIMARY_DARK,
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipCommon,
                        callbacks: {
                            label: ctx => '  {{ $simbolo }} ' + new Intl.NumberFormat('es-GT', { minimumFractionDigits: 2 }).format(ctx.parsed.y),
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(196, 168, 182, 0.4)', drawBorder: false },
                        ticks: {
                            font: { size: 11 },
                            callback: v => '{{ $simbolo }} ' + v.toLocaleString('es-GT'),
                        }
                    }
                }
            }
        });
    }

    /* ============== 2. DONUT: Estado de citas ============== */
    const donutData = @json($donutData);
    const ctxD = document.getElementById('chartEstadoCitas')?.getContext('2d');
    if (ctxD) {
        const datosFiltrados = donutData.filter(d => d.value > 0);

        new Chart(ctxD, {
            type: 'doughnut',
            data: {
                labels: datosFiltrados.map(d => d.label),
                datasets: [{
                    data: datosFiltrados.map(d => d.value),
                    backgroundColor: datosFiltrados.map(d => d.color),
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 12,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 11 },
                        }
                    },
                    tooltip: {
                        ...tooltipCommon,
                        callbacks: {
                            label: ctx => '  ' + ctx.label + ': ' + ctx.parsed + ' citas',
                        }
                    }
                }
            }
        });
    }

    /* ============== 3. BARRAS HORIZONTALES: Top servicios ============== */
    const topServicios = @json($topServicios);
    const ctxS = document.getElementById('chartTopServicios')?.getContext('2d');
    if (ctxS && topServicios.length > 0) {
        const colores = ['#a35880', '#7a3d5e', '#a87f48', '#4d8b58', '#487da0', '#c47736'];

        new Chart(ctxS, {
            type: 'bar',
            data: {
                labels: topServicios.map(s => s.descripcion.length > 28 ? s.descripcion.substring(0, 28) + '…' : s.descripcion),
                datasets: [{
                    label: 'Citas',
                    data: topServicios.map(s => s.total),
                    backgroundColor: topServicios.map((_, i) => colores[i % colores.length]),
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 22,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipCommon,
                        callbacks: {
                            label: ctx => '  ' + ctx.parsed.x + ' citas · {{ $simbolo }} ' +
                                          new Intl.NumberFormat('es-GT', { minimumFractionDigits: 2 }).format(topServicios[ctx.dataIndex].ingresos || 0),
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(196, 168, 182, 0.4)' },
                        ticks: { font: { size: 11 }, precision: 0 }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '500' } }
                    }
                }
            }
        });
    }

    /* ============== 4. PIE: Métodos de pago ============== */
    const metodos = @json($ventasPorMetodo);
    const ctxM = document.getElementById('chartMetodosPago')?.getContext('2d');
    if (ctxM && metodos.length > 0) {
        const labelsMetodo = {
            'efectivo': 'Efectivo',
            'tarjeta': 'Tarjeta',
            'transferencia': 'Transferencia',
            'mixto': 'Mixto',
            'otro': 'Otro',
        };
        const colorsMetodo = {
            'efectivo': '#4d8b58',
            'tarjeta': '#487da0',
            'transferencia': '#a87f48',
            'mixto': '#a35880',
            'otro': '#5d4858',
        };

        new Chart(ctxM, {
            type: 'pie',
            data: {
                labels: metodos.map(m => labelsMetodo[m.metodo_pago] || m.metodo_pago),
                datasets: [{
                    data: metodos.map(m => parseFloat(m.total)),
                    backgroundColor: metodos.map(m => colorsMetodo[m.metodo_pago] || '#888'),
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 14,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 11 },
                        }
                    },
                    tooltip: {
                        ...tooltipCommon,
                        callbacks: {
                            label: ctx => '  ' + ctx.label + ': {{ $simbolo }} ' +
                                          new Intl.NumberFormat('es-GT', { minimumFractionDigits: 2 }).format(ctx.parsed),
                        }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
