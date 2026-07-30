@if($configEmpresa)
    @php
        $primario = $configEmpresa->color_primario ?? '#a35880';
        $secundario = $configEmpresa->color_secundario ?? '#4a3050';
        $accent = $configEmpresa->color_accent ?? '#a87f48';
        $sidebarFondo = $configEmpresa->color_sidebar_fondo ?? '#2e1c33';
        $sidebarTexto = $configEmpresa->color_sidebar_texto ?? '#f0e4ea';
        $menuActivo = $configEmpresa->color_menu_activo ?? '#4a3050';
        $fondo = $configEmpresa->color_fondo ?? '#cdb1be';
    @endphp
    <style>
        :root {
            --spa-primary: {{ $primario }};
            --spa-primary-dark: {{ \App\Models\ConfiguracionEmpresa::oscurecer($primario, 0.22) }};
            --spa-primary-darker: {{ \App\Models\ConfiguracionEmpresa::oscurecer($primario, 0.42) }};
            --spa-secondary: {{ $secundario }};
            --spa-secondary-dark: {{ \App\Models\ConfiguracionEmpresa::oscurecer($secundario, 0.35) }};
            --spa-accent: {{ $accent }};
            --spa-accent-dark: {{ \App\Models\ConfiguracionEmpresa::oscurecer($accent, 0.25) }};
            --spa-sidebar-bg: {{ $sidebarFondo }};
            --spa-menu-activo: {{ $menuActivo }};
            --spa-sidebar-text: {{ $sidebarTexto }};
            --spa-bg: {{ $fondo }};
        }
    </style>
@endif
