<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · {{ $configEmpresa->nombre_empresa ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v=3" rel="stylesheet">
</head>
<body>
<div class="spa-auth-wrapper">
    <div class="spa-auth-card">
        <div class="spa-logo">
            @if($configEmpresa?->logoUrl())
                <img src="{{ $configEmpresa->logoUrl() }}" alt="Logo" style="max-height:80px;border-radius:14px;">
            @else
                <div class="icon"><i class="bi bi-flower2"></i></div>
            @endif
            <h1>{{ $configEmpresa->nombre_empresa ?? 'TPV Estética y SPA' }}</h1>
            <p class="subtitle">Inicia sesión para acceder al panel</p>
        </div>

        @if(session('status'))
            <div class="alert alert-info" data-autohide>
                <i class="bi bi-info-circle"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;">
                        <i class="bi bi-envelope text-spa-secondary"></i>
                    </span>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                           placeholder="tucorreo@ejemplo.com"
                           autofocus required>
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;">
                        <i class="bi bi-lock text-spa-secondary"></i>
                    </span>
                    <input id="password" type="password" name="password"
                           class="form-control border-start-0 @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label" style="font-size:.88rem;color:var(--spa-muted)">Recordarme</label>
                </div>
            </div>

            <button type="submit" class="btn btn-spa-primary btn-block">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
        </form>

        <hr style="margin:1.5rem 0;border-color:var(--spa-border)">

        <div style="font-size:.78rem;color:var(--spa-muted);text-align:center;line-height:1.6">
            <strong style="color:var(--spa-secondary)">Credenciales de prueba:</strong><br>
            admin@estetica.local / admin123<br>
            recepcion@estetica.local / recepcion123
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}?v=3"></script>
</body>
</html>
