<?php

namespace App\Http\Controllers;

use App\Models\Bono;
use App\Models\Cita;
use App\Models\CitaServicio;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();

        /* ============== KPIs ============== */
        $stats = [
            'citas_hoy'       => Cita::whereDate('fecha', $hoy)
                                    ->whereNotIn('estado', ['cancelada'])
                                    ->count(),
            'ventas_hoy'      => (float) Venta::whereDate('fecha', $hoy)
                                    ->where('estado', 'pagada')
                                    ->sum('total'),
            'clientes_nuevos' => Cliente::where('created_at', '>=', $inicioSemana)->count(),
            'bonos_activos'   => Bono::where('estado', 'activo')->count(),
            'profesionales'   => User::where('rol', 'profesional')->where('activo', true)->count(),
            'usuarios_total'  => User::where('activo', true)->count(),
            'productos_bajo'  => Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
                                    ->where('activo', true)->count(),
            'ventas_mes'      => (float) Venta::where('estado', 'pagada')
                                    ->whereMonth('fecha', now()->month)
                                    ->whereYear('fecha', now()->year)
                                    ->sum('total'),
        ];

        /* ============== GRÁFICO 1: Ventas últimos 14 días ============== */
        $ventasPorDia = collect();
        for ($i = 13; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $total = (float) Venta::whereDate('fecha', $fecha)
                ->where('estado', 'pagada')
                ->sum('total');
            $ventasPorDia->push([
                'fecha' => $fecha->format('Y-m-d'),
                'label' => $fecha->locale('es')->isoFormat('D MMM'),
                'total' => $total,
            ]);
        }

        /* ============== GRÁFICO 2: Citas por estado (últimos 30 días) ============== */
        $citasPorEstado = Cita::selectRaw('estado, COUNT(*) as total')
            ->whereDate('fecha', '>=', Carbon::today()->subDays(30))
            ->whereDate('fecha', '<=', Carbon::today()->addDays(15))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $estadosLabel = [
            'pendiente'  => 'Pendientes',
            'confirmada' => 'Confirmadas',
            'realizada'  => 'Realizadas',
            'cancelada'  => 'Canceladas',
            'no_show'    => 'No asistieron',
        ];
        $estadosColor = [
            'pendiente'  => '#c47736',
            'confirmada' => '#487da0',
            'realizada'  => '#4d8b58',
            'cancelada'  => '#b04848',
            'no_show'    => '#7a3d5e',
        ];

        $donutData = [];
        foreach ($estadosLabel as $key => $label) {
            $donutData[] = [
                'label' => $label,
                'value' => (int) ($citasPorEstado[$key] ?? 0),
                'color' => $estadosColor[$key],
            ];
        }

        /* ============== GRÁFICO 3: Top servicios (últimos 30 días) ============== */
        $topServicios = CitaServicio::selectRaw('cita_servicios.descripcion, COUNT(*) as total, SUM(cita_servicios.precio) as ingresos')
            ->join('citas', 'citas.id', '=', 'cita_servicios.cita_id')
            ->whereDate('citas.fecha', '>=', Carbon::today()->subDays(30))
            ->where('citas.estado', '!=', 'cancelada')
            ->groupBy('cita_servicios.descripcion')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        /* ============== GRÁFICO 4: Ventas por método de pago (mes actual) ============== */
        $ventasPorMetodo = Venta::selectRaw('metodo_pago, SUM(total) as total, COUNT(*) as cantidad')
            ->where('estado', 'pagada')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->groupBy('metodo_pago')
            ->get();

        /* ============== Próximas citas ============== */
        $proximasCitas = Cita::with(['cliente', 'profesional', 'cabina', 'servicios'])
            ->whereDate('fecha', '>=', $hoy)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit(6)
            ->get();

        /* ============== Top profesionales (últimos 30 días) ============== */
        $topProfesionales = Cita::selectRaw('profesional_id, COUNT(*) as total_citas, SUM(total) as ingresos')
            ->with('profesional:id,name,rol')
            ->whereNotNull('profesional_id')
            ->whereDate('fecha', '>=', Carbon::today()->subDays(30))
            ->where('estado', 'realizada')
            ->groupBy('profesional_id')
            ->orderByDesc('total_citas')
            ->limit(4)
            ->get();

        $accesosRapidos = [
            ['titulo' => 'Nueva cita',     'icono' => 'calendar-plus', 'ruta' => route('citas.create'), 'color' => 'primary'],
            ['titulo' => 'Punto de venta', 'icono' => 'cash-stack',    'ruta' => route('ventas.tpv'),   'color' => 'success'],
            ['titulo' => 'Nuevo cliente',  'icono' => 'person-plus',   'ruta' => route('clientes.create'), 'color' => 'info'],
            ['titulo' => 'Vender bono',    'icono' => 'gift',          'ruta' => route('bonos.create'), 'color' => 'warning'],
        ];

        return view('dashboard.index', compact(
            'stats', 'ventasPorDia', 'donutData', 'topServicios', 'ventasPorMetodo',
            'proximasCitas', 'topProfesionales', 'accesosRapidos', 'hoy'
        ));
    }
}
