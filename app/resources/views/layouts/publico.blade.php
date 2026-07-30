<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Comprobante') · {{ $configEmpresa->nombre_empresa ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v=3" rel="stylesheet">

    @include('layouts.partials.theme-colors')

    @stack('styles')
</head>
<body style="background:var(--spa-bg)">
    <div class="spa-content" style="max-width:640px;margin:0 auto;padding:1.75rem 1rem">
        @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><div>{{ session('error') }}</div></div>
        @endif

        @yield('contenido')
    </div>
</body>
</html>
