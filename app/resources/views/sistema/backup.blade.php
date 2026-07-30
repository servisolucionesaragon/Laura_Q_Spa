@extends('layouts.app')

@section('titulo', 'Copia de seguridad y mantenimiento')

@push('styles')
<style>
    .backup-hero {
        background: linear-gradient(120deg, var(--spa-secondary-dark) 0%, var(--spa-secondary) 50%, var(--spa-primary-dark) 100%);
        color: #fff;
        border-radius: var(--spa-radius);
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--spa-shadow-lg);
    }
    .backup-hero::after {
        content: '';
        position: absolute;
        right: -50px; top: -50px;
        width: 240px; height: 240px;
        background: radial-gradient(circle, rgba(255,255,255,.18), transparent 70%);
        border-radius: 50%;
    }
    .backup-hero h2 { color: #fff; font-weight: 600; margin: 0 0 .35rem; }
    .backup-hero p  { color: rgba(255,255,255,.85); margin: 0; max-width: 720px; }
    .backup-hero .stats {
        display: flex; gap: 1.5rem; margin-top: 1.25rem; flex-wrap: wrap;
        position: relative; z-index: 2;
    }
    .backup-hero .stat-block {
        background: rgba(255,255,255,.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 12px;
        padding: .85rem 1.15rem;
        min-width: 160px;
    }
    .backup-hero .stat-block .label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: .85;
    }
    .backup-hero .stat-block .value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.1;
        margin-top: .25rem;
    }

    .backup-tile {
        background: var(--spa-surface);
        border: 1px solid var(--spa-border);
        border-radius: var(--spa-radius);
        padding: 1.5rem;
        height: 100%;
        position: relative;
        transition: transform .18s ease, box-shadow .18s ease;
        box-shadow: var(--spa-shadow-soft);
    }
    .backup-tile:hover {
        transform: translateY(-3px);
        box-shadow: var(--spa-shadow-lg);
    }
    .backup-tile .tile-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 1rem;
        box-shadow: 0 8px 20px rgba(40,20,38,.18);
    }
    .backup-tile.create .tile-icon  { background: linear-gradient(135deg, var(--spa-primary), var(--spa-primary-dark)); }
    .backup-tile.restore .tile-icon { background: linear-gradient(135deg, var(--spa-info), #3e7090); }
    .backup-tile.reset .tile-icon   { background: linear-gradient(135deg, var(--spa-danger), #8a3a3a); }
    .backup-tile h3 { color: var(--spa-secondary); margin: 0 0 .35rem; font-weight: 600; font-size: 1.1rem; }
    .backup-tile p  { color: var(--spa-muted); font-size: .9rem; margin: 0 0 1.1rem; }

    .backup-list { list-style: none; padding: 0; margin: 0; }
    .backup-item {
        display: flex; align-items: center; gap: 1rem;
        padding: .85rem 1rem;
        border: 1px solid var(--spa-border-soft);
        border-radius: var(--spa-radius-sm);
        background: var(--spa-surface);
        margin-bottom: .6rem;
        transition: all .18s ease;
    }
    .backup-item:hover {
        border-color: var(--spa-primary);
        background: var(--spa-surface-soft);
    }
    .backup-item .file-ic {
        width: 44px; height: 44px;
        border-radius: 10px;
        background: var(--spa-secondary);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
    }
    .backup-item .file-info { flex: 1; min-width: 0; }
    .backup-item .file-name {
        font-weight: 600;
        color: var(--spa-secondary);
        word-break: break-all;
    }
    .backup-item .file-meta {
        font-size: .8rem;
        color: var(--spa-muted);
    }
    .backup-item .file-actions { display: flex; gap: .4rem; flex-shrink: 0; }

    .danger-zone {
        background: linear-gradient(135deg, #fef0ee 0%, #fadcdc 100%);
        border: 2px solid var(--spa-danger);
        border-radius: var(--spa-radius);
        padding: 1.5rem;
    }
    .danger-zone h4 {
        color: var(--spa-danger);
        margin: 0 0 .35rem;
        font-weight: 700;
        display: flex; align-items: center; gap: .5rem;
    }

    .reset-option {
        border: 2px solid var(--spa-border);
        border-radius: var(--spa-radius-sm);
        padding: 1.1rem;
        cursor: pointer;
        transition: all .18s ease;
        background: var(--spa-surface);
    }
    .reset-option:hover { border-color: var(--spa-primary); }
    .reset-option.selected {
        border-color: var(--spa-primary-dark);
        background: var(--spa-surface-soft);
        box-shadow: 0 0 0 4px rgba(184,118,154,.18);
    }
    .reset-option .opt-title {
        font-weight: 600;
        color: var(--spa-secondary);
        margin-bottom: .25rem;
    }
    .reset-option .opt-desc {
        font-size: .85rem;
        color: var(--spa-muted);
    }
    .reset-option .opt-list {
        margin-top: .65rem; padding-left: 1.2rem;
        font-size: .82rem; color: var(--spa-muted);
    }
    .reset-option .opt-list li { margin-bottom: .2rem; }

    /* Modal */
    .modal-content {
        border-radius: var(--spa-radius);
        border: none;
        box-shadow: var(--spa-shadow-lg);
    }
    .modal-header {
        border-bottom: 2px solid var(--spa-border-soft);
        padding: 1.1rem 1.5rem;
    }
    .modal-header.danger { background: var(--spa-danger); color: #fff; border-radius: var(--spa-radius) var(--spa-radius) 0 0; }
    .modal-header.danger .btn-close { filter: invert(1); }
    .modal-title { font-weight: 600; color: var(--spa-secondary); }
    .modal-header.danger .modal-title { color: #fff; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { border-top: 1px solid var(--spa-border-soft); padding: 1rem 1.5rem; }

    .confirm-input {
        font-family: 'Courier New', monospace;
        letter-spacing: 1.5px;
        text-align: center;
        font-weight: 700;
        background: var(--spa-bg) !important;
    }
</style>
@endpush

@section('contenido')
    {{-- HERO --}}
    <div class="backup-hero">
        <h2><i class="bi bi-shield-check"></i> Copia de seguridad y mantenimiento</h2>
        <p>Genera respaldos completos del sistema, restaura desde una copia anterior o resetea el sistema para iniciar con una empresa nueva. Las acciones destructivas requieren confirmación explícita.</p>
        <div class="stats">
            <div class="stat-block">
                <div class="label"><i class="bi bi-archive"></i> Copias</div>
                <div class="value">{{ $totalBackups }}</div>
            </div>
            <div class="stat-block">
                <div class="label"><i class="bi bi-hdd"></i> Espacio usado</div>
                <div class="value">
                    @php
                        $b = $tamanoTotal;
                        if ($b < 1048576) echo round($b/1024, 1) . ' KB';
                        elseif ($b < 1073741824) echo round($b/1048576, 2) . ' MB';
                        else echo round($b/1073741824, 2) . ' GB';
                    @endphp
                </div>
            </div>
            <div class="stat-block">
                <div class="label"><i class="bi bi-clock-history"></i> Última copia</div>
                <div class="value" style="font-size:.95rem">
                    {{ $backups[0]['fecha'] ?? '— Aún no hay copias —' }}
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="spa-card">
        <div class="spa-tabs" data-target="#tabs-backup">
            <button type="button" class="tab active" data-pane="t-crear"><i class="bi bi-plus-circle"></i> Crear copia</button>
            <button type="button" class="tab" data-pane="t-restaurar"><i class="bi bi-arrow-counterclockwise"></i> Restaurar</button>
            <button type="button" class="tab" data-pane="t-reset"><i class="bi bi-exclamation-octagon"></i> Resetear sistema</button>
        </div>

        <div id="tabs-backup">
            {{-- ========== CREAR ========== --}}
            <div class="tab-pane active" id="t-crear">
                <div class="row g-3">
                    <div class="col-12 col-lg-5">
                        <div class="backup-tile create">
                            <div class="tile-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                            <h3>Generar copia ahora</h3>
                            <p>Crea un archivo ZIP con la base de datos completa y los archivos del sistema (logos, imágenes). Útil antes de actualizaciones o cambios importantes.</p>

                            <ul style="font-size:.85rem;color:var(--spa-muted);padding-left:1.1rem;margin-bottom:1rem">
                                <li>Incluye estructura y datos de todas las tablas</li>
                                <li>Incluye archivos cargados (logo, imágenes)</li>
                                <li>Formato ZIP portable estándar</li>
                                <li>Se almacena en <code>storage/app/backups/</code></li>
                            </ul>

                            <form method="POST" action="{{ route('sistema.backup.crear') }}">
                                @csrf
                                <button type="submit" class="btn btn-spa-primary btn-block"
                                        onclick="this.disabled=true; this.innerHTML='<i class=&quot;bi bi-hourglass-split&quot;></i> Generando...'; this.form.submit();">
                                    <i class="bi bi-download"></i> Generar copia ahora
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-12 col-lg-7">
                        <div class="backup-tile">
                            <h3 style="margin-bottom:1rem"><i class="bi bi-folder2-open text-spa-primary"></i> Copias existentes</h3>

                            @if(empty($backups))
                                <div class="text-center" style="padding:2.5rem 1rem;color:var(--spa-muted)">
                                    <i class="bi bi-archive" style="font-size:3rem;opacity:.5"></i>
                                    <p style="margin:.85rem 0 0">No hay copias generadas todavía.</p>
                                    <small>Crea tu primera copia con el botón de la izquierda.</small>
                                </div>
                            @else
                                <ul class="backup-list">
                                    @foreach($backups as $b)
                                        <li class="backup-item">
                                            <div class="file-ic"><i class="bi bi-file-earmark-zip"></i></div>
                                            <div class="file-info">
                                                <div class="file-name">{{ $b['nombre'] }}</div>
                                                <div class="file-meta">
                                                    <i class="bi bi-calendar"></i> {{ $b['fecha'] }}
                                                    · <i class="bi bi-hdd"></i> {{ $b['tamano_h'] }}
                                                </div>
                                            </div>
                                            <div class="file-actions">
                                                <a href="{{ route('sistema.backup.descargar', $b['nombre']) }}"
                                                   class="btn btn-spa-secondary btn-sm" title="Descargar">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm" style="background:var(--spa-info);color:#fff"
                                                        title="Restaurar desde esta copia"
                                                        onclick="prepararRestaurar('{{ $b['nombre'] }}')">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm" style="background:var(--spa-danger);color:#fff"
                                                        title="Eliminar"
                                                        onclick="prepararEliminar('{{ $b['nombre'] }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== RESTAURAR ========== --}}
            <div class="tab-pane" id="t-restaurar">
                <div class="alert alert-warning" style="margin-bottom:1.25rem">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Atención:</strong> Restaurar el sistema sobreescribirá <strong>toda</strong> la información actual
                        (clientes, citas, ventas, configuración, etc.). Te recomendamos generar una copia previa antes de continuar.
                        Tras la restauración serás desconectado y deberás volver a iniciar sesión.
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="backup-tile restore">
                            <div class="tile-icon"><i class="bi bi-cloud-arrow-down"></i></div>
                            <h3>Restaurar desde archivo</h3>
                            <p>Sube un archivo de respaldo (.zip) generado previamente desde este sistema.</p>

                            <form method="POST" action="{{ route('sistema.backup.restaurar') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Archivo de copia (.zip)</label>
                                    <input type="file" name="archivo" accept=".zip" class="form-control" required>
                                    <div class="form-text">Tamaño máximo: 50 MB</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Para confirmar, escribe <strong>RESTAURAR</strong>
                                    </label>
                                    <input type="text" name="confirmacion" class="form-control confirm-input"
                                           placeholder="RESTAURAR" required>
                                </div>
                                <button type="submit" class="btn btn-block" style="background:var(--spa-info);color:#fff">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restaurar sistema
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="backup-tile restore">
                            <div class="tile-icon" style="background:linear-gradient(135deg, var(--spa-secondary), var(--spa-secondary-dark))">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                            <h3>Restaurar desde copia existente</h3>
                            <p>Selecciona una de las copias guardadas en el servidor para restaurarla.</p>

                            @if(empty($backups))
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <div>No hay copias guardadas. Crea una primero o sube un archivo desde tu equipo.</div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('sistema.backup.restaurar') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Copia a restaurar</label>
                                        <select name="backup_existente" class="form-select" required>
                                            <option value="">— Selecciona una copia —</option>
                                            @foreach($backups as $b)
                                                <option value="{{ $b['nombre'] }}">
                                                    {{ $b['nombre'] }} · {{ $b['tamano_h'] }} · {{ $b['fecha'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Para confirmar, escribe <strong>RESTAURAR</strong>
                                        </label>
                                        <input type="text" name="confirmacion" class="form-control confirm-input"
                                               placeholder="RESTAURAR" required>
                                    </div>
                                    <button type="submit" class="btn btn-block" style="background:var(--spa-secondary);color:#fff">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar copia seleccionada
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== RESET ========== --}}
            <div class="tab-pane" id="t-reset">
                <div class="danger-zone">
                    <h4><i class="bi bi-exclamation-octagon-fill"></i> Zona peligrosa</h4>
                    <p style="color:#7a2f2f;margin:0 0 1.25rem">
                        Estas acciones eliminan datos de forma <strong>permanente</strong>. No se pueden deshacer.
                        Te recomendamos <strong>marcar la opción de copia previa</strong> para tener un respaldo automático antes de resetear.
                    </p>

                    <form method="POST" action="{{ route('sistema.reset') }}" id="form-reset">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="reset-option" id="opt-soft">
                                    <input type="radio" name="tipo" value="soft" checked
                                           onchange="seleccionarReset('soft')" style="display:none">
                                    <div class="opt-title">
                                        <i class="bi bi-eraser"></i> Reset suave
                                        <span class="spa-badge warning" style="margin-left:.4rem">Recomendado</span>
                                    </div>
                                    <div class="opt-desc">Borra solo los datos operacionales. Conserva catálogos, empleados y configuración.</div>
                                    <ul class="opt-list">
                                        <li>✓ Borra: ventas, citas, bonos vendidos, movimientos de stock</li>
                                        <li>✓ Mantiene: clientes, productos, servicios, empleados, cabinas, configuración</li>
                                    </ul>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="reset-option" id="opt-hard"
                                       style="border-color:var(--spa-danger)">
                                    <input type="radio" name="tipo" value="hard"
                                           onchange="seleccionarReset('hard')" style="display:none">
                                    <div class="opt-title" style="color:var(--spa-danger)">
                                        <i class="bi bi-trash3-fill"></i> Reset completo (empresa nueva)
                                    </div>
                                    <div class="opt-desc">Borra <strong>todo</strong> excepto tu usuario administrador. Para empezar de cero.</div>
                                    <ul class="opt-list">
                                        <li>✗ Borra: ventas, citas, bonos, clientes</li>
                                        <li>✗ Borra: productos, servicios, proveedores, cabinas</li>
                                        <li>✗ Borra: empleados (excepto tu cuenta) y configuración de empresa</li>
                                    </ul>
                                </label>
                            </div>
                        </div>

                        <div class="form-check mb-3" style="background:#fff;padding:.85rem 1rem 0.85rem 2.4rem;border-radius:8px;border:1px solid var(--spa-border)">
                            <input type="checkbox" name="crear_backup_previo" id="crear_backup_previo"
                                   class="form-check-input" value="1" checked>
                            <label for="crear_backup_previo" class="form-check-label" style="font-weight:500">
                                <i class="bi bi-shield-check text-spa-secondary"></i>
                                Generar copia automática antes de resetear (muy recomendado)
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="color:var(--spa-danger)">
                                Para confirmar, escribe <strong id="palabra-confirm">RESETEAR</strong>
                            </label>
                            <input type="text" name="confirmacion" id="confirmacion-input" class="form-control confirm-input" required
                                   placeholder="RESETEAR" autocomplete="off">
                            <div id="confirmacion-error" class="text-danger mt-1" style="font-size:.85rem;display:none">
                                <i class="bi bi-exclamation-circle"></i> <span></span>
                            </div>
                        </div>

                        <button type="button" id="btn-abrir-reset" class="btn" style="background:var(--spa-danger);color:#fff;font-weight:600">
                            <i class="bi bi-exclamation-octagon"></i> Ejecutar reset
                        </button>
                        <small class="text-spa-muted ms-2">Esta acción no se puede deshacer.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal eliminar --}}
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header danger">
                    <h5 class="modal-title"><i class="bi bi-trash"></i> Eliminar copia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Seguro que deseas eliminar esta copia de seguridad?</p>
                    <p style="background:var(--spa-bg);padding:.7rem 1rem;border-radius:8px;font-family:monospace;font-size:.9rem"
                       id="modalEliminarNombre"></p>
                    <p class="text-spa-muted" style="font-size:.85rem">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-spa-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('sistema.backup.eliminar') }}" class="m-0 d-inline">
                        @csrf
                        <input type="hidden" name="nombre" id="inputEliminarNombre">
                        <button type="submit" class="btn" style="background:var(--spa-danger);color:#fff">
                            <i class="bi bi-trash"></i> Eliminar definitivamente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal confirmación de reset (en vez de confirm()/alert() nativos del navegador,
         que quedan bloqueados en silencio si el usuario marcó alguna vez "Evitar que
         este sitio cree más cuadros de diálogo" en Chrome) --}}
    <div class="modal fade" id="modalConfirmarReset" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header danger">
                    <h5 class="modal-title" id="modalResetTitulo"><i class="bi bi-exclamation-octagon-fill"></i> Confirmar reset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="modalResetTexto" style="font-weight:500"></p>
                    <p class="text-spa-muted" style="font-size:.85rem">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-spa-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnConfirmarResetFinal" class="btn" style="background:var(--spa-danger);color:#fff">
                        <i class="bi bi-exclamation-octagon"></i> Sí, ejecutar reset
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Selección de tipo de reset (cambia palabra de confirmación)
    function seleccionarReset(tipo) {
        document.querySelectorAll('.reset-option').forEach(el => el.classList.remove('selected'));
        document.getElementById('opt-' + tipo).classList.add('selected');
        document.getElementById('palabra-confirm').textContent = tipo === 'hard' ? 'BORRAR TODO' : 'RESETEAR';
        const input = document.querySelector('input[name=confirmacion]');
        input.placeholder = tipo === 'hard' ? 'BORRAR TODO' : 'RESETEAR';
        input.value = '';
    }
    seleccionarReset('soft');

    // Modal eliminar
    function prepararEliminar(nombre) {
        document.getElementById('modalEliminarNombre').textContent = nombre;
        document.getElementById('inputEliminarNombre').value = nombre;
        new bootstrap.Modal(document.getElementById('modalEliminar')).show();
    }

    // Cambiar a tab restaurar y preseleccionar archivo
    function prepararRestaurar(nombre) {
        document.querySelectorAll('.spa-tabs .tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelector('[data-pane="t-restaurar"]').classList.add('active');
        document.getElementById('t-restaurar').classList.add('active');

        const select = document.querySelector('select[name=backup_existente]');
        if (select) select.value = nombre;
        window.scrollTo({ top: document.querySelector('.spa-card').offsetTop, behavior: 'smooth' });
    }

    // Confirmación del reset vía modal propio (no confirm()/alert() nativos:
    // si el usuario marcó alguna vez "Evitar que este sitio cree más cuadros
    // de diálogo" en Chrome, esas funciones devuelven false/undefined en
    // silencio para SIEMPRE en esa pestaña, y el formulario nunca se envía
    // sin mostrar ningún error visible).
    const formReset = document.getElementById('form-reset');
    const btnAbrirReset = document.getElementById('btn-abrir-reset');
    const inputConfirmacion = document.getElementById('confirmacion-input');
    const errorConfirmacion = document.getElementById('confirmacion-error');

    btnAbrirReset?.addEventListener('click', function () {
        const tipo = document.querySelector('input[name=tipo]:checked').value;
        const palabra = tipo === 'hard' ? 'BORRAR TODO' : 'RESETEAR';
        const valor = inputConfirmacion.value.trim();

        if (valor !== palabra) {
            errorConfirmacion.querySelector('span').textContent = 'Para confirmar debes escribir exactamente: ' + palabra;
            errorConfirmacion.style.display = 'block';
            inputConfirmacion.focus();
            return;
        }
        errorConfirmacion.style.display = 'none';

        document.getElementById('modalResetTexto').textContent = tipo === 'hard'
            ? '⚠️ ÚLTIMO AVISO: esto borrará TODOS los datos del sistema (ventas, citas, clientes, productos, empleados y configuración), excepto tu usuario. ¿Continuar?'
            : '⚠️ ¿Confirmas borrar todos los datos operacionales (ventas, citas, bonos)? Los catálogos, empleados y configuración se mantienen.';

        new bootstrap.Modal(document.getElementById('modalConfirmarReset')).show();
    });

    document.getElementById('btnConfirmarResetFinal')?.addEventListener('click', function () {
        formReset.submit();
    });
</script>
@endpush
