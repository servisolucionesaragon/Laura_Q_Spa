<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Inicio') · {{ $configEmpresa->nombre_empresa ?? config('app.name') }}</title>

    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ route('pwa.icon', ['size' => 192]) }}">
    <link rel="icon" href="{{ route('pwa.icon', ['size' => 192]) }}">
    <meta name="theme-color" content="{{ $configEmpresa->color_primario ?? '#ff78a2' }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="{{ $configEmpresa->nombre_empresa ?? config('app.name') }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v=3" rel="stylesheet">

    @if($configEmpresa)
        <style>
            :root {
                --spa-primary: {{ $configEmpresa->color_primario ?? '#d4a5c0' }};
                --spa-secondary: {{ $configEmpresa->color_secundario ?? '#8b6f8e' }};
            }
        </style>
    @endif

    @stack('styles')
</head>
<body>
    <div class="spa-app">
        @include('layouts.partials.sidebar')

        <div class="spa-overlay"></div>

        <main class="spa-main">
            @include('layouts.partials.topbar')

            <div class="spa-content">
                @if(session('success'))
                    <div class="alert alert-success" data-autohide>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" data-autohide>
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @yield('contenido')
            </div>

            <footer class="text-center py-3 text-spa-muted" style="font-size:.82rem">
                © {{ date('Y') }} {{ $configEmpresa->nombre_empresa ?? config('app.name') }} · Software TPV Estética &amp; SPA
            </footer>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}?v=3"></script>
    @stack('scripts')
</body>
</html>
