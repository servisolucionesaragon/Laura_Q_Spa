<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BonoController;
use App\Http\Controllers\BonoPlantillaController;
use App\Http\Controllers\CabinaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\CategoriaTratamientoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SistemaController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// PWA (instalable en el navegador) — públicas, sin auth
Route::get('manifest.webmanifest', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('pwa-icon/{size}.png', [PwaController::class, 'icon'])->whereIn('size', ['192', '512'])->name('pwa.icon');

// Recibos públicos firmados (para compartir por WhatsApp sin necesitar cuenta)
Route::get('r/venta/{venta}', [VentaController::class, 'reciboPublico'])
    ->middleware('signed')->name('publico.venta.recibo');
Route::get('r/caja/{caja}', [CajaController::class, 'reportePublico'])
    ->middleware('signed')->name('publico.caja.reporte');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Zona autenticada
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ======= CRM / Operativa ======= */

    // Clientes
    Route::get('clientes/buscar', [ClienteController::class, 'buscarAjax'])->name('clientes.buscar');
    Route::resource('clientes', ClienteController::class);

    // Citas (agenda)
    Route::get('citas/lista', [CitaController::class, 'lista'])->name('citas.lista');
    Route::post('citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');
    Route::resource('citas', CitaController::class);

    // Ventas / TPV
    Route::get('ventas/tpv', [VentaController::class, 'tpv'])->name('ventas.tpv');
    Route::resource('ventas', VentaController::class)->only(['index', 'store', 'show']);
    // Editar/anular ventas: solo administrador (ver grupo role:admin más abajo)
    Route::resource('metodos-pago', MetodoPagoController::class)->except(['show', 'create', 'edit'])
        ->parameters(['metodos-pago' => 'metodoPago']);

    // Caja diaria (apertura/cierre, gastos e ingresos)
    Route::get('caja', [CajaController::class, 'index'])->name('caja.index');
    Route::get('caja/abrir', [CajaController::class, 'formAbrir'])->name('caja.abrir');
    Route::post('caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir.store');
    Route::get('caja/{caja}', [CajaController::class, 'show'])->name('caja.show');
    Route::get('caja/{caja}/reporte', [CajaController::class, 'reporte'])->name('caja.reporte');
    Route::post('caja/{caja}/movimientos', [CajaController::class, 'agregarMovimiento'])->name('caja.movimientos.store');
    Route::delete('caja/{caja}/movimientos/{movimiento}', [CajaController::class, 'eliminarMovimiento'])->name('caja.movimientos.destroy');
    Route::post('caja/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

    // Bonos
    Route::resource('bonos-plantillas', BonoPlantillaController::class)->except(['show'])
        ->parameters(['bonos-plantillas' => 'bonoPlantilla']);
    Route::post('bonos/{bono}/consumir', [BonoController::class, 'consumir'])->name('bonos.consumir');
    Route::resource('bonos', BonoController::class)->except(['edit', 'update']);

    /* ======= Catálogos ======= */

    // Tratamientos
    Route::resource('categorias-tratamientos', CategoriaTratamientoController::class)->except(['show', 'create', 'edit'])
        ->parameters(['categorias-tratamientos' => 'categoriaTratamiento']);
    Route::resource('tratamientos', TratamientoController::class);

    // Productos
    Route::resource('categorias-productos', CategoriaProductoController::class)->except(['show', 'create', 'edit'])
        ->parameters(['categorias-productos' => 'categoriaProducto']);
    Route::get('productos/{producto}/movimientos', [ProductoController::class, 'movimientos'])->name('productos.movimientos');
    Route::post('productos/{producto}/stock', [ProductoController::class, 'moverStock'])->name('productos.stock');
    Route::resource('productos', ProductoController::class);

    // Proveedores
    Route::resource('proveedores', ProveedorController::class);

    /* ======= Equipo ======= */
    Route::resource('empleados', EmpleadoController::class);
    Route::resource('cabinas', CabinaController::class)->except(['show', 'create', 'edit']);

    /* ======= Reportes ======= */
    Route::get('reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('reportes/indicadores', [ReporteController::class, 'indicadores'])->name('reportes.indicadores');

    /* ======= Sistema (sólo admin) ======= */
    Route::middleware('role:admin')->group(function () {
        // Editar/anular ventas
        Route::get('ventas/{venta}/edit', [VentaController::class, 'edit'])->name('ventas.edit');
        Route::put('ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update');
        Route::post('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');

        Route::get('configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::post('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

        Route::resource('usuarios', UsuarioController::class);

        Route::prefix('sistema')->name('sistema.')->group(function () {
            Route::get('backup',                       [SistemaController::class, 'backup'])->name('backup');
            Route::post('backup/crear',                [SistemaController::class, 'crearBackup'])->name('backup.crear');
            Route::get('backup/descargar/{nombre}',    [SistemaController::class, 'descargarBackup'])->name('backup.descargar');
            Route::post('backup/eliminar',             [SistemaController::class, 'eliminarBackup'])->name('backup.eliminar');
            Route::post('backup/restaurar',            [SistemaController::class, 'restaurarBackup'])->name('backup.restaurar');
            Route::post('reset',                       [SistemaController::class, 'resetSistema'])->name('reset');
        });
    });
});
