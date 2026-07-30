@extends('layouts.app')

@section('titulo', 'Configuración del sistema')

@section('contenido')
    <form method="POST" action="{{ route('configuracion.update') }}" enctype="multipart/form-data" novalidate>
        @csrf

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Revisa los siguientes campos:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="spa-card">
            <div class="spa-card-header">
                <div>
                    <h3><i class="bi bi-gear text-spa-primary"></i> Configuración</h3>
                    <small class="text-spa-muted">Datos de la empresa, moneda, impuestos y preferencias del sistema</small>
                </div>
                <button type="submit" class="btn btn-spa-primary">
                    <i class="bi bi-save"></i> Guardar cambios
                </button>
            </div>

            <div class="spa-tabs" data-target="#tabs-config">
                <button type="button" class="tab active" data-pane="tab-empresa"><i class="bi bi-building"></i> Empresa</button>
                <button type="button" class="tab" data-pane="tab-marca"><i class="bi bi-palette"></i> Marca &amp; Logo</button>
                <button type="button" class="tab" data-pane="tab-moneda"><i class="bi bi-currency-exchange"></i> Moneda &amp; Impuestos</button>
                <button type="button" class="tab" data-pane="tab-horario"><i class="bi bi-clock-history"></i> Horario</button>
                <button type="button" class="tab" data-pane="tab-otros"><i class="bi bi-card-text"></i> Otros</button>
            </div>

            <div id="tabs-config">
                {{-- ====== EMPRESA ====== --}}
                <div class="tab-pane active" id="tab-empresa">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre del negocio *</label>
                            <input type="text" name="nombre_empresa" class="form-control"
                                   value="{{ old('nombre_empresa', $configuracion->nombre_empresa) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Razón social</label>
                            <input type="text" name="razon_social" class="form-control"
                                   value="{{ old('razon_social', $configuracion->razon_social) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NIT / RFC / Identificación fiscal</label>
                            <input type="text" name="nit_rfc" class="form-control"
                                   value="{{ old('nit_rfc', $configuracion->nit_rfc) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                   value="{{ old('telefono', $configuracion->telefono) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $configuracion->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sitio web</label>
                            <input type="text" name="sitio_web" class="form-control"
                                   value="{{ old('sitio_web', $configuracion->sitio_web) }}"
                                   placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control"
                                   value="{{ old('direccion', $configuracion->direccion) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control"
                                   value="{{ old('ciudad', $configuracion->ciudad) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">País</label>
                            <input type="text" name="pais" class="form-control"
                                   value="{{ old('pais', $configuracion->pais) }}">
                        </div>
                    </div>
                </div>

                {{-- ====== MARCA & LOGO ====== --}}
                <div class="tab-pane" id="tab-marca">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4 text-center">
                            <label class="form-label d-block">Logo actual</label>
                            <div style="background:var(--spa-bg);border:2px dashed var(--spa-border);border-radius:14px;padding:1rem;min-height:180px;display:flex;align-items:center;justify-content:center">
                                <img id="logo-preview"
                                     src="{{ $configuracion->logoUrl() ?? 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text x=%2250%22 y=%2255%22 font-size=%2240%22 text-anchor=%22middle%22>🌸</text></svg>' }}"
                                     alt="Logo"
                                     style="max-width:160px;max-height:160px;border-radius:12px;object-fit:contain">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label" for="logo">Subir logo (PNG, JPG, SVG, WEBP · máx 2MB)</label>
                                <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                <div class="form-text">Recomendado: imagen cuadrada, mínimo 200x200 px.</div>
                            </div>
                            @if($configuracion->logo)
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="eliminar_logo" id="eliminar_logo" class="form-check-input" value="1">
                                    <label for="eliminar_logo" class="form-check-label" style="font-size:.9rem;color:var(--spa-danger)">
                                        <i class="bi bi-trash"></i> Eliminar el logo actual
                                    </label>
                                </div>
                            @endif

                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Color primario</label>
                                    <div class="form-text mb-1">Botones, enlaces y acentos principales.</div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" name="color_primario" class="form-control form-control-color" style="width:60px"
                                               value="{{ old('color_primario', $configuracion->color_primario) }}">
                                        <input type="text" class="form-control" readonly
                                               value="{{ old('color_primario', $configuracion->color_primario) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Color secundario</label>
                                    <div class="form-text mb-1">Títulos, topbar y el ítem activo del menú lateral.</div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" name="color_secundario" class="form-control form-control-color" style="width:60px"
                                               value="{{ old('color_secundario', $configuracion->color_secundario) }}">
                                        <input type="text" class="form-control" readonly
                                               value="{{ old('color_secundario', $configuracion->color_secundario) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Color de acento</label>
                                    <div class="form-text mb-1">Íconos y detalles decorativos (íconos de tarjetas, resplandor del login).</div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" name="color_accent" class="form-control form-control-color" style="width:60px"
                                               value="{{ old('color_accent', $configuracion->color_accent ?? '#a87f48') }}">
                                        <input type="text" class="form-control" readonly
                                               value="{{ old('color_accent', $configuracion->color_accent ?? '#a87f48') }}">
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <label class="form-label">Fondo del menú lateral</label>
                                    <div class="form-text mb-1">Color de fondo de la barra de navegación.</div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" name="color_sidebar_fondo" class="form-control form-control-color" style="width:60px"
                                               value="{{ old('color_sidebar_fondo', $configuracion->color_sidebar_fondo ?? '#2e1c33') }}">
                                        <input type="text" class="form-control" readonly
                                               value="{{ old('color_sidebar_fondo', $configuracion->color_sidebar_fondo ?? '#2e1c33') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Texto del menú lateral</label>
                                    <div class="form-text mb-1">Color del texto/íconos sobre el fondo del menú.</div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" name="color_sidebar_texto" class="form-control form-control-color" style="width:60px"
                                               value="{{ old('color_sidebar_texto', $configuracion->color_sidebar_texto ?? '#f0e4ea') }}">
                                        <input type="text" class="form-control" readonly
                                               value="{{ old('color_sidebar_texto', $configuracion->color_sidebar_texto ?? '#f0e4ea') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle"></i> Los tonos oscuros para hovers y degradados
                                (botones, tarjetas) se calculan automáticamente a partir de estos colores base.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ====== MONEDA & IMPUESTOS ====== --}}
                <div class="tab-pane" id="tab-moneda">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Símbolo de moneda *</label>
                            <input type="text" name="simbolo_moneda" class="form-control" maxlength="5"
                                   value="{{ old('simbolo_moneda', $configuracion->simbolo_moneda) }}" required>
                            <div class="form-text">Ej. Q, $, €, £</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Código ISO *</label>
                            <input type="text" name="codigo_moneda" class="form-control" maxlength="5"
                                   value="{{ old('codigo_moneda', $configuracion->codigo_moneda) }}" required>
                            <div class="form-text">Ej. GTQ, USD, EUR</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Formato de visualización *</label>
                            <select name="formato_moneda" class="form-select" required>
                                <option value="symbol_amount" {{ $configuracion->formato_moneda === 'symbol_amount' ? 'selected' : '' }}>
                                    Símbolo antes ({{ $configuracion->simbolo_moneda }} 1,234.56)
                                </option>
                                <option value="amount_symbol" {{ $configuracion->formato_moneda === 'amount_symbol' ? 'selected' : '' }}>
                                    Símbolo después (1,234.56 {{ $configuracion->simbolo_moneda }})
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre del impuesto *</label>
                            <input type="text" name="nombre_impuesto" class="form-control"
                                   value="{{ old('nombre_impuesto', $configuracion->nombre_impuesto) }}" required>
                            <div class="form-text">Ej. IVA, ISV, IGV</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">% Impuesto *</label>
                            <input type="number" step="0.01" min="0" max="100" name="impuesto_porcentaje"
                                   class="form-control"
                                   value="{{ old('impuesto_porcentaje', $configuracion->impuesto_porcentaje) }}" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" id="impuesto_incluido" name="impuesto_incluido"
                                       class="form-check-input" value="1"
                                       {{ $configuracion->impuesto_incluido ? 'checked' : '' }}>
                                <label for="impuesto_incluido" class="form-check-label">
                                    Precios incluyen impuesto
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ====== HORARIO ====== --}}
                <div class="tab-pane" id="tab-horario">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Zona horaria *</label>
                            <select name="zona_horaria" class="form-select" required>
                                @php
                                    $zonas = ['America/Guatemala','America/Mexico_City','America/Bogota','America/Lima','America/Santiago','America/Buenos_Aires','America/New_York','Europe/Madrid','Europe/London','UTC'];
                                @endphp
                                @foreach($zonas as $tz)
                                    <option value="{{ $tz }}" {{ $configuracion->zona_horaria === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hora de apertura *</label>
                            <input type="time" name="hora_apertura" class="form-control"
                                   value="{{ old('hora_apertura', \Carbon\Carbon::parse($configuracion->hora_apertura)->format('H:i')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hora de cierre *</label>
                            <input type="time" name="hora_cierre" class="form-control"
                                   value="{{ old('hora_cierre', \Carbon\Carbon::parse($configuracion->hora_cierre)->format('H:i')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Intervalo de citas (minutos) *</label>
                            <input type="number" min="5" max="240" name="intervalo_citas_min" class="form-control"
                                   value="{{ old('intervalo_citas_min', $configuracion->intervalo_citas_min) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label d-block">Días laborales</label>
                            @php
                                $dias = ['lun'=>'Lun','mar'=>'Mar','mie'=>'Mié','jue'=>'Jue','vie'=>'Vie','sab'=>'Sáb','dom'=>'Dom'];
                                $activos = old('dias_laborales', $configuracion->dias_laborales ?? []);
                            @endphp
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($dias as $valor => $etq)
                                    <label class="form-check d-flex align-items-center gap-2 px-3 py-2"
                                           style="background:{{ in_array($valor,$activos) ? 'rgba(212,165,192,0.18)' : 'var(--spa-bg)' }};
                                                  border:1px solid var(--spa-border);border-radius:30px;cursor:pointer">
                                        <input type="checkbox" name="dias_laborales[]" value="{{ $valor }}"
                                               class="form-check-input m-0"
                                               {{ in_array($valor,$activos) ? 'checked' : '' }}>
                                        <span style="font-size:.88rem">{{ $etq }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ====== OTROS ====== --}}
                <div class="tab-pane" id="tab-otros">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Mensaje al pie del recibo / ticket</label>
                            <textarea name="mensaje_recibo" class="form-control" rows="3"
                                      maxlength="500">{{ old('mensaje_recibo', $configuracion->mensaje_recibo) }}</textarea>
                            <div class="form-text">Este mensaje aparecerá impreso al final de cada ticket de venta.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Términos y condiciones</label>
                            <textarea name="terminos_condiciones" class="form-control" rows="6">{{ old('terminos_condiciones', $configuracion->terminos_condiciones) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="border-color:var(--spa-border)">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-spa-secondary">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-spa-primary">
                    <i class="bi bi-save"></i> Guardar cambios
                </button>
            </div>
        </div>
    </form>
@endsection
