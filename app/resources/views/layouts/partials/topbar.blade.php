<header class="spa-topbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="toggle-sidebar" aria-label="Menú">
            <i class="bi bi-list" style="font-size:1.4rem"></i>
        </button>
        <h1 class="page-title">@yield('titulo', 'Dashboard')</h1>
    </div>

    <div class="topbar-right">
        @auth
            @php
                $esProfesional = auth()->user()->rol === 'profesional';
                $inminentes = $citasProximas->filter(fn ($c) => $c->minutosParaEmpezar() <= 60)->count();
            @endphp
            <div class="dropdown">
                <button class="btn btn-spa-secondary position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Citas de hoy">
                    <i class="bi bi-bell"></i>
                    @if($citasProximas->isNotEmpty())
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:{{ $inminentes > 0 ? 'var(--spa-danger)' : 'var(--spa-primary)' }};font-size:.65rem">
                            {{ $citasProximas->count() }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-2" style="min-width:320px;max-height:400px;overflow-y:auto">
                    <div class="px-2 py-1" style="font-weight:700;color:var(--spa-secondary);font-size:.85rem">
                        {{ $esProfesional ? 'Mis citas de hoy' : 'Citas de hoy' }}
                    </div>
                    @if($citasProximas->isEmpty())
                        <div class="px-2 py-3 text-center text-spa-muted" style="font-size:.85rem">
                            <i class="bi bi-calendar-check" style="font-size:1.5rem;opacity:.4"></i>
                            <p class="mb-0 mt-1">No hay citas pendientes hoy.</p>
                        </div>
                    @else
                        @foreach($citasProximas as $c)
                            @php $min = $c->minutosParaEmpezar(); @endphp
                            <a href="{{ route('citas.show', $c) }}" class="dropdown-item" style="white-space:normal;border-radius:8px;margin-bottom:2px">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong style="font-size:.88rem">{{ \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') }} · {{ $c->cliente?->nombre_completo }}</strong>
                                        @if(! $esProfesional)
                                            <div style="font-size:.76rem;color:var(--spa-muted)">{{ $c->profesional?->name ?? 'Sin asignar' }}</div>
                                        @endif
                                    </div>
                                    @if($min >= 0 && $min <= 60)
                                        <span class="spa-badge danger" style="font-size:.68rem">en {{ $min }} min</span>
                                    @elseif($min < 0)
                                        <span class="spa-badge warning" style="font-size:.68rem">en curso/atrasada</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

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
