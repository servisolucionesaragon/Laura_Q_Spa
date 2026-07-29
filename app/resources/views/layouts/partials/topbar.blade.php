<header class="spa-topbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="toggle-sidebar" aria-label="Menú">
            <i class="bi bi-list" style="font-size:1.4rem"></i>
        </button>
        <h1 class="page-title">@yield('titulo', 'Dashboard')</h1>
    </div>

    <div class="topbar-right">
        @auth
            <div class="user-chip">
                <div class="avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div style="font-weight:500">{{ auth()->user()->name }}</div>
                    <small>{{ auth()->user()->rol_nombre }}</small>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-spa-secondary" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        @endauth
    </div>
</header>
