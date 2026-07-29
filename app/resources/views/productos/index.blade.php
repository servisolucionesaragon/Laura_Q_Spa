@extends('layouts.app')
@section('titulo', 'Productos & Stock')

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="spa-card">
    <div class="spa-card-header">
        <div>
            <h3><i class="bi bi-box-seam text-spa-primary"></i> Productos & Stock</h3>
            <small class="text-spa-muted">Inventario de productos para venta y uso interno.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('categorias-productos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-tags"></i> Categorías</a>
            <a href="{{ route('productos.create') }}" class="btn btn-spa-primary"><i class="bi bi-plus-lg"></i> Nuevo producto</a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5"><input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por nombre o código..."></div>
        <div class="col-md-3">
            <select name="categoria_id" class="form-select">
                <option value="">— Todas las categorías —</option>
                @foreach($categorias as $c)<option value="{{ $c->id }}" {{ (string) $categoria === (string) $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <div class="form-check"><input type="checkbox" name="stock_bajo" id="sb" class="form-check-input" value="1" {{ $stockBajo ? 'checked' : '' }}><label class="form-check-label" for="sb">Sólo stock bajo</label></div>
        </div>
        <div class="col-md-2"><button class="btn btn-spa-secondary btn-block"><i class="bi bi-search"></i> Filtrar</button></div>
    </form>

    @if($productos->isEmpty())
        <div class="text-center py-4 text-spa-muted"><i class="bi bi-box" style="font-size:2.5rem;opacity:.4"></i><p>Sin productos.</p></div>
    @else
        <div class="table-responsive">
            <table class="spa-table">
                <thead><tr><th>Código</th><th>Producto</th><th>Categoría</th><th>P. compra</th><th>P. venta</th><th>Stock</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                @foreach($productos as $p)
                    <tr>
                        <td><code>{{ $p->codigo ?? '—' }}</code></td>
                        <td><strong>{{ $p->nombre }}</strong>@if($p->proveedor)<br><small class="text-spa-muted">{{ $p->proveedor->nombre }}</small>@endif</td>
                        <td>@if($p->categoria)<span class="spa-badge">{{ $p->categoria->nombre }}</span>@else—@endif</td>
                        <td>{{ $sim }} {{ number_format($p->precio_compra, 2) }}</td>
                        <td><strong>{{ $sim }} {{ number_format($p->precio_venta, 2) }}</strong></td>
                        <td>
                            <strong>{{ $p->stock_actual }}</strong> {{ $p->unidad }}
                            @if($p->stock_actual <= 0)
                                <span class="spa-badge danger">Sin stock</span>
                            @elseif($p->stock_actual <= $p->stock_minimo)
                                <span class="spa-badge warning">Bajo (mín {{ $p->stock_minimo }})</span>
                            @endif
                        </td>
                        <td>@if($p->activo)<span class="spa-badge success">Activo</span>@else<span class="spa-badge danger">Inactivo</span>@endif</td>
                        <td class="text-end" style="white-space:nowrap">
                            <button class="btn btn-sm" style="background:var(--spa-success);color:#fff" data-bs-toggle="modal" data-bs-target="#mStock"
                                    onclick='abrirStock(@json($p))' title="Mover stock">
                                <i class="bi bi-arrow-down-up"></i>
                            </button>
                            <a href="{{ route('productos.movimientos', $p) }}" class="btn btn-sm" style="background:var(--spa-info);color:#fff" title="Historial">
                                <i class="bi bi-clock-history"></i>
                            </a>
                            <a href="{{ route('productos.edit', $p) }}" class="btn btn-spa-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('productos.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:var(--spa-danger);color:#fff"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $productos->links() }}</div>
    @endif
</div>

{{-- Modal mover stock --}}
<div class="modal fade" id="mStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formStock" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-arrow-down-up"></i> Mover stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-info" style="font-size:.9rem">
                    Producto: <strong id="mPNombre"></strong> — Stock actual: <strong id="mPStock">0</strong>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo de movimiento *</label>
                    <select name="tipo" class="form-select" required>
                        <option value="entrada">Entrada (compra/recepción)</option>
                        <option value="salida">Salida (uso/merma)</option>
                        <option value="ajuste">Ajuste (cantidad final exacta)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad *</label>
                    <input type="number" name="cantidad" class="form-control" required min="1" value="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Motivo</label>
                    <input type="text" name="motivo" class="form-control" maxlength="100" placeholder="Ej. Compra a proveedor, ajuste por inventario...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-spa-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-spa-primary"><i class="bi bi-check"></i> Registrar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function abrirStock(p) {
    document.getElementById('formStock').action = "{{ url('productos') }}/" + p.id + "/stock";
    document.getElementById('mPNombre').textContent = p.nombre;
    document.getElementById('mPStock').textContent = p.stock_actual + ' ' + p.unidad;
}
</script>
@endpush
