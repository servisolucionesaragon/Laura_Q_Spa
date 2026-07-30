@extends('layouts.app')
@section('titulo', 'Editar venta ' . $venta->numero)

@push('styles')
<style>
    .tpv-grid { display: grid; grid-template-columns: 1fr 380px; gap: 1rem; }
    @media (max-width: 1200px) { .tpv-grid { grid-template-columns: 1fr; } }

    .tpv-tabs { display: flex; gap: .25rem; margin-bottom: 1rem; }
    .tpv-tabs .tpv-tab {
        padding: .55rem 1rem;
        border: 1px solid var(--spa-border);
        background: var(--spa-surface);
        border-radius: var(--spa-radius-sm);
        cursor: pointer;
        color: var(--spa-muted);
        font-weight: 500;
        font-size: .9rem;
    }
    .tpv-tabs .tpv-tab.active {
        background: var(--spa-secondary);
        color: #fff;
        border-color: var(--spa-secondary);
    }

    .tpv-search { margin-bottom: .85rem; }

    .tpv-products { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .65rem; max-height: 520px; overflow-y: auto; padding: .25rem; }
    .tpv-prod {
        background: var(--spa-surface);
        border: 1px solid var(--spa-border);
        border-radius: var(--spa-radius-sm);
        padding: .85rem .7rem;
        text-align: center;
        cursor: pointer;
        transition: all .15s ease;
        font-size: .85rem;
    }
    .tpv-prod:hover {
        border-color: var(--spa-primary);
        transform: translateY(-2px);
        box-shadow: var(--spa-shadow-soft);
    }
    .tpv-prod .nombre { font-weight: 600; color: var(--spa-secondary); margin-bottom: .25rem; min-height: 2.4em; line-height: 1.2; }
    .tpv-prod .precio { color: var(--spa-primary-dark); font-weight: 700; }
    .tpv-prod .meta { font-size: .72rem; color: var(--spa-muted); }
    .tpv-prod .stock-bajo { color: var(--spa-danger); }

    .cart {
        background: var(--spa-surface);
        border: 1px solid var(--spa-border);
        border-radius: var(--spa-radius);
        padding: 1.25rem;
        position: sticky;
        top: 90px;
        box-shadow: var(--spa-shadow);
    }
    .cart-items { max-height: 280px; overflow-y: auto; margin-bottom: .85rem; }
    .cart-item {
        display: flex; align-items: center; gap: .5rem;
        padding: .55rem;
        border-bottom: 1px solid var(--spa-border-soft);
        font-size: .85rem;
    }
    .cart-item .info { flex: 1; min-width: 0; }
    .cart-item .info strong { color: var(--spa-secondary); display: block; line-height: 1.2; }
    .cart-item .info small { color: var(--spa-muted); }
    .cart-item .controls { display: flex; align-items: center; gap: .25rem; flex-shrink: 0; }
    .cart-item input[type=number] { width: 50px; text-align: center; padding: 2px 4px; font-size: .85rem; border: 1px solid var(--spa-border); border-radius: 4px; }
    .cart-item .btn-x { background: var(--spa-danger); color: #fff; border: none; width: 24px; height: 24px; border-radius: 6px; font-size: .75rem; }

    .totales { background: var(--spa-bg-light); border-radius: var(--spa-radius-sm); padding: .9rem 1rem; }
    .totales .line { display: flex; justify-content: space-between; font-size: .9rem; color: var(--spa-muted); padding: .15rem 0; }
    .totales .line.grand { border-top: 2px solid var(--spa-border); margin-top: .5rem; padding-top: .55rem; font-size: 1.4rem; font-weight: 800; color: var(--spa-secondary); }
</style>
@endpush

@section('contenido')
<div class="alert alert-info no-print">
    <i class="bi bi-pencil-square"></i>
    <div>Editando la venta <strong>{{ $venta->numero }}</strong>. Al guardar se recalcula el stock de productos según los cambios.</div>
</div>

<div class="tpv-grid">
    {{-- Catálogo --}}
    <div class="spa-card" style="margin-bottom:0">
        <div class="spa-card-header">
            <h3><i class="bi bi-pencil-square text-spa-primary"></i> Editar venta {{ $venta->numero }}</h3>
            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-spa-secondary"><i class="bi bi-x-lg"></i> Cancelar</a>
        </div>

        <div class="tpv-tabs">
            <button class="tpv-tab active" onclick="cambiarTab('servicios', this)"><i class="bi bi-flower2"></i> Servicios</button>
            <button class="tpv-tab" onclick="cambiarTab('productos', this)"><i class="bi bi-box-seam"></i> Productos</button>
        </div>

        <div class="tpv-search">
            <input type="text" id="busqueda" class="form-control" placeholder="Buscar...">
        </div>

        <div id="tab-servicios">
            <div class="tpv-products" id="grid-servicios">
                @foreach($tratamientos as $t)
                    <div class="tpv-prod" data-nombre="{{ strtolower($t->nombre) }}"
                         onclick='agregarItem({"tipo":"servicio","id":{{ $t->id }},"descripcion":"{{ addslashes($t->nombre) }}","precio":{{ $t->precio }}})'>
                        <div class="nombre">{{ $t->nombre }}</div>
                        <div class="meta">{{ $t->duracion_min }} min</div>
                        <div class="precio">{{ $configEmpresa?->formatearMoneda($t->precio) ?? ('$ ' . number_format($t->precio, 2)) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="tab-productos" style="display:none">
            <div class="tpv-products" id="grid-productos">
                @foreach($productos as $p)
                    <div class="tpv-prod" data-nombre="{{ strtolower($p->nombre) }}"
                         onclick='agregarItem({"tipo":"producto","id":{{ $p->id }},"descripcion":"{{ addslashes($p->nombre) }}","precio":{{ $p->precio_venta }},"stock":{{ $p->stock_actual }}})'>
                        <div class="nombre">{{ $p->nombre }}</div>
                        <div class="meta {{ $p->stock_actual <= 0 ? 'stock-bajo' : '' }}">
                            {{ $p->stock_actual }} {{ $p->unidad }} disp.
                        </div>
                        <div class="precio">{{ $configEmpresa?->formatearMoneda($p->precio_venta) ?? ('$ ' . number_format($p->precio_venta, 2)) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Carrito --}}
    <div>
        <form method="POST" action="{{ route('ventas.update', $venta) }}" id="formVenta">
            @csrf
            @method('PUT')
            <div class="cart">
                <h4 style="color:var(--spa-secondary);margin:0 0 .85rem"><i class="bi bi-cart3"></i> Carrito <span id="contador" class="spa-badge">0</span></h4>

                <div class="mb-2">
                    @include('clientes.partials.buscador', [
                        'nombreCampo'  => 'cliente_id',
                        'idInput'      => 'tpvCliente',
                        'requerido'    => false,
                        'placeholder'  => 'Cliente eventual (buscar)...',
                        'clienteId'    => $venta->cliente_id,
                        'clienteTexto' => $venta->cliente?->nombre_completo,
                    ])
                </div>

                <div class="cart-items" id="cart-items">
                    <div id="vacio" style="text-align:center;color:var(--spa-muted);padding:2rem 1rem;font-size:.9rem">
                        <i class="bi bi-cart-x" style="font-size:2rem;opacity:.4"></i>
                        <p style="margin-top:.5rem">Selecciona servicios o productos</p>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label" style="font-size:.78rem;margin-bottom:.2rem">Descuento</label>
                    <input type="number" name="descuento" id="descuento" class="form-control form-control-sm" step="0.01" min="0" value="{{ $venta->descuento }}" onchange="calcular()">
                </div>

                <div class="mb-2">
                    <label class="form-label" style="font-size:.78rem;margin-bottom:.2rem">Método de pago</label>
                    <select name="metodo_pago" class="form-select form-select-sm" required>
                        @forelse($metodosPago as $mp)
                            <option value="{{ $mp->nombre }}" {{ $venta->metodo_pago === $mp->nombre ? 'selected' : '' }}>{{ ucfirst($mp->nombre) }}</option>
                        @empty
                            <option value="efectivo">Efectivo</option>
                        @endforelse
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label" style="font-size:.78rem;margin-bottom:.2rem">Notas</label>
                    <textarea name="notas" class="form-control form-control-sm" rows="2">{{ $venta->notas }}</textarea>
                </div>

                @php($monedaCero = $configEmpresa?->formatearMoneda(0) ?? '$ 0.00')
                <div class="totales">
                    <div class="line"><span>Subtotal</span><strong id="t-sub">{{ $monedaCero }}</strong></div>
                    <div class="line"><span>Descuento</span><strong id="t-desc">{{ $monedaCero }}</strong></div>
                    <div class="line grand"><span>Total</span><strong id="t-total">{{ $monedaCero }}</strong></div>
                </div>

                <button type="submit" class="btn btn-spa-primary btn-block mt-3" id="btnCobrar" disabled>
                    <i class="bi bi-save"></i> Guardar cambios
                </button>
            </div>

            <div id="items-container"></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const MONEDA_SIMBOLO = @json($configEmpresa->simbolo_moneda ?? '$');
    const MONEDA_FORMATO = @json($configEmpresa->formato_moneda ?? 'symbol_amount');
    function formatearMoneda(monto) {
        const num = Number(monto).toFixed(2);
        return MONEDA_FORMATO === 'amount_symbol' ? `${num} ${MONEDA_SIMBOLO}` : `${MONEDA_SIMBOLO} ${num}`;
    }

    let carrito = @json($venta->items->map(fn ($it) => [
        'tipo' => $it->tipo,
        'id' => $it->referencia_id,
        'descripcion' => $it->descripcion,
        'precio' => (float) $it->precio_unitario,
        'cantidad' => (float) $it->cantidad,
    ]));

    function cambiarTab(tab, btn) {
        document.querySelectorAll('.tpv-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-servicios').style.display  = tab === 'servicios' ? '' : 'none';
        document.getElementById('tab-productos').style.display  = tab === 'productos' ? '' : 'none';
        document.getElementById('busqueda').value = '';
        filtrarProductos();
    }

    document.getElementById('busqueda').addEventListener('input', filtrarProductos);
    function filtrarProductos() {
        const q = document.getElementById('busqueda').value.toLowerCase();
        document.querySelectorAll('.tpv-prod').forEach(el => {
            el.style.display = el.dataset.nombre.includes(q) ? '' : 'none';
        });
    }

    function agregarItem(it) {
        const existente = carrito.find(c => c.tipo === it.tipo && c.id === it.id);
        if (existente) existente.cantidad++;
        else carrito.push({ ...it, cantidad: 1 });
        render();
    }

    function quitarItem(idx) {
        carrito.splice(idx, 1);
        render();
    }

    function cambiarCantidad(idx, val) {
        const v = parseFloat(val) || 0;
        if (v <= 0) quitarItem(idx);
        else { carrito[idx].cantidad = v; render(); }
    }

    function render() {
        const cont = document.getElementById('cart-items');
        const vacio = document.getElementById('vacio');
        cont.innerHTML = '';

        if (carrito.length === 0) {
            cont.appendChild(vacio);
        } else {
            carrito.forEach((it, idx) => {
                const el = document.createElement('div');
                el.className = 'cart-item';
                el.innerHTML = `
                    <div class="info">
                        <strong>${it.descripcion}</strong>
                        <small>${it.tipo === 'servicio' ? '🌸' : '📦'} ${formatearMoneda(it.precio)} c/u</small>
                    </div>
                    <div class="controls">
                        <input type="number" min="${it.tipo === 'producto' ? '1' : '0.01'}" step="${it.tipo === 'producto' ? '1' : '0.01'}" value="${it.cantidad}"
                               onchange="cambiarCantidad(${idx}, this.value)">
                        <button type="button" class="btn-x" onclick="quitarItem(${idx})">×</button>
                    </div>
                `;
                cont.appendChild(el);
            });
        }
        document.getElementById('contador').textContent = carrito.length;
        document.getElementById('btnCobrar').disabled = carrito.length === 0;
        calcular();
        sincronizarItems();
    }

    function calcular() {
        const sub = carrito.reduce((s, it) => s + (it.precio * it.cantidad), 0);
        const desc = parseFloat(document.getElementById('descuento').value) || 0;
        const total = Math.max(0, sub - desc);
        document.getElementById('t-sub').textContent = formatearMoneda(sub);
        document.getElementById('t-desc').textContent = formatearMoneda(desc);
        document.getElementById('t-total').textContent = formatearMoneda(total);
    }

    function sincronizarItems() {
        const cont = document.getElementById('items-container');
        cont.innerHTML = '';
        carrito.forEach((it, idx) => {
            cont.innerHTML += `
                <input type="hidden" name="items[${idx}][tipo]" value="${it.tipo}">
                <input type="hidden" name="items[${idx}][referencia_id]" value="${it.id}">
                <input type="hidden" name="items[${idx}][descripcion]" value="${it.descripcion.replace(/"/g, '&quot;')}">
                <input type="hidden" name="items[${idx}][cantidad]" value="${it.cantidad}">
                <input type="hidden" name="items[${idx}][precio_unitario]" value="${it.precio}">
            `;
        });
    }

    render();
</script>
@endpush
