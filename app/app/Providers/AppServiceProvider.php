<?php

namespace App\Providers;

use App\Models\Cita;
use App\Models\ConfiguracionEmpresa;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // Compartir la configuración de la empresa con todas las vistas
        View::composer('*', function ($view) {
            $config = null;
            try {
                if (Schema::hasTable('configuracion_empresa')) {
                    $config = ConfiguracionEmpresa::first();
                }
            } catch (\Throwable $e) {
                $config = null;
            }
            $view->with('configEmpresa', $config);
        });

        // Campana de citas del día (propias si es profesional, todas si no) para el topbar
        View::composer('layouts.partials.topbar', function ($view) {
            $citasProximas = collect();
            try {
                if (Auth::check() && Schema::hasTable('citas')) {
                    $citasProximas = Cita::proximasParaUsuario(Auth::user());
                }
            } catch (\Throwable $e) {
                $citasProximas = collect();
            }
            $view->with('citasProximas', $citasProximas);
        });
    }
}
