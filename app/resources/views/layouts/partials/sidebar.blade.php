@php
    $rutaActual = request()->route()?->getName() ?? '';
    $logo = $configEmpresa?->logoUrl();
    $nombre = $configEmpresa?->nombre_empresa ?? config('app.name');
@endphp
<aside class="spa-sidebar">
    <div class="brand">
        @if($logo)
            <img src="{{ $logo }}" alt="Logo" style="width:42px;height:42px;border-radius:12px;object-fit:cover;">
        @else
            <div class="brand-icon"><i class="bi bi-flower2"></i></div>
        @endif
        <div class="brand-text">
            <strong>{{ \Illuminate\Support\Str::limit($nombre, 18) }}</strong>
            <small>Estética &amp; SPA</small>
        </div>
    </div>

    <div class="nav-section">Principal</div>
    <a href="{{ route('dashboard') }}" class="nav-link {{ $rutaActual === 'dashboard' ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">Operativa</div>
    <a href="{{ route('citas.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'citas') ? 'active' : '' }}">
        <i class="bi bi-calendar-week"></i> Agenda &amp; Citas
    </a>
    <a href="{{ route('ventas.tpv') }}" class="nav-link {{ str_starts_with($rutaActual, 'ventas') ? 'active' : '' }}">
        <i class="bi bi-cash-coin"></i> Punto de Venta
    </a>
    <a href="{{ route('caja.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'caja') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i> Caja
    </a>
    <a href="{{ route('clientes.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'clientes') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Clientes
    </a>
    <a href="{{ route('bonos.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'bonos') ? 'active' : '' }}">
        <i class="bi bi-gift"></i> Bonos
    </a>
    <a href="{{ route('tratamientos.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'tratamientos') || str_starts_with($rutaActual, 'categorias-tratamientos') ? 'active' : '' }}">
        <i class="bi bi-heart-pulse"></i> Servicios
    </a>

    <div class="nav-section">Inventario</div>
    <a href="{{ route('productos.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'productos') || str_starts_with($rutaActual, 'categorias-productos') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Productos &amp; Stock
    </a>
    <a href="{{ route('proveedores.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'proveedores') ? 'active' : '' }}">
        <i class="bi bi-truck"></i> Proveedores
    </a>

    <div class="nav-section">Equipo</div>
    <a href="{{ route('empleados.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'empleados') ? 'active' : '' }}">
        <i class="bi bi-person-badge"></i> Empleados
    </a>
    <a href="{{ route('cabinas.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'cabinas') ? 'active' : '' }}">
        <i class="bi bi-door-open"></i> Cabinas
    </a>

    <div class="nav-section">Reportes</div>
    <a href="{{ route('reportes.ventas') }}" class="nav-link {{ str_starts_with($rutaActual, 'reportes.ventas') ? 'active' : '' }}">
        <i class="bi bi-graph-up-arrow"></i> Ventas
    </a>
    <a href="{{ route('reportes.indicadores') }}" class="nav-link {{ str_starts_with($rutaActual, 'reportes.indicadores') ? 'active' : '' }}">
        <i class="bi bi-bar-chart"></i> Indicadores
    </a>

    @auth
        @if(auth()->user()->esAdmin())
            <div class="nav-section">Sistema</div>
            <a href="{{ route('configuracion.edit') }}" class="nav-link {{ str_starts_with($rutaActual, 'configuracion') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Configuración
            </a>
            <a href="{{ route('usuarios.index') }}" class="nav-link {{ str_starts_with($rutaActual, 'usuarios') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> Usuarios
            </a>
            <a href="{{ route('sistema.backup') }}" class="nav-link {{ str_starts_with($rutaActual, 'sistema.') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Copia de seguridad
            </a>
        @endif
    @endauth
</aside>
