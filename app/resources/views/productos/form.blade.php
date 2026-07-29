@extends('layouts.app')
@section('titulo', $producto->exists ? 'Editar producto' : 'Nuevo producto')

@section('contenido')
@include('layouts.partials.errors')

<form method="POST" action="{{ $producto->exists ? route('productos.update', $producto) : route('productos.store') }}">
    @csrf @if($producto->exists) @method('PUT') @endif

    <div class="spa-card">
        <div class="spa-card-header">
            <h3><i class="bi bi-box-seam text-spa-primary"></i> {{ $producto->exists ? 'Editar producto' : 'Nuevo producto' }}</h3>
            <a href="{{ route('productos.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Código</label><input type="text" name="codigo" class="form-control" value="{{ old('codigo', $producto->codigo) }}"></div>
            <div class="col-md-9"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" required value="{{ old('nombre', $producto->nombre) }}"></div>

            <div class="col-md-6"><label class="form-label">Categoría</label>
                <select name="categoria_id" class="form-select">
                    <option value="">— Sin categoría —</option>
                    @foreach($categorias as $c)<option value="{{ $c->id }}" {{ (string) old('categoria_id', $producto->categoria_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6"><label class="form-label">Proveedor</label>
                <select name="proveedor_id" class="form-select">
                    <option value="">— Sin proveedor —</option>
                    @foreach($proveedores as $p)<option value="{{ $p->id }}" {{ (string) old('proveedor_id', $producto->proveedor_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>@endforeach
                </select>
            </div>

            <div class="col-md-3"><label class="form-label">Precio compra *</label><input type="number" step="0.01" name="precio_compra" class="form-control" required min="0" value="{{ old('precio_compra', $producto->precio_compra) }}"></div>
            <div class="col-md-3"><label class="form-label">Precio venta *</label><input type="number" step="0.01" name="precio_venta" class="form-control" required min="0" value="{{ old('precio_venta', $producto->precio_venta) }}"></div>
            <div class="col-md-2"><label class="form-label">{{ $producto->exists ? 'Stock' : 'Stock inicial' }}</label><input type="number" name="stock_actual" class="form-control" min="0" value="{{ old('stock_actual', $producto->stock_actual) }}" {{ $producto->exists ? 'readonly' : '' }}>@if($producto->exists)<small class="form-text">Edítalo desde "Mover stock"</small>@endif</div>
            <div class="col-md-2"><label class="form-label">Stock mínimo</label><input type="number" name="stock_minimo" class="form-control" min="0" value="{{ old('stock_minimo', $producto->stock_minimo) }}"></div>
            <div class="col-md-2"><label class="form-label">Unidad</label><input type="text" name="unidad" class="form-control" value="{{ old('unidad', $producto->unidad ?? 'unidad') }}"></div>

            <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $producto->descripcion) }}</textarea></div>

            <div class="col-12 d-flex gap-3 flex-wrap">
                <div class="form-check"><input type="checkbox" name="para_venta" id="pv" class="form-check-input" value="1" {{ old('para_venta', $producto->para_venta ?? true) ? 'checked' : '' }}><label class="form-check-label" for="pv">Disponible para venta</label></div>
                <div class="form-check"><input type="checkbox" name="para_uso_interno" id="pu" class="form-check-input" value="1" {{ old('para_uso_interno', $producto->para_uso_interno) ? 'checked' : '' }}><label class="form-check-label" for="pu">Uso interno (no se vende)</label></div>
                <div class="form-check"><input type="checkbox" name="activo" id="ac" class="form-check-input" value="1" {{ old('activo', $producto->activo ?? true) ? 'checked' : '' }}><label class="form-check-label" for="ac">Activo</label></div>
            </div>
        </div>
        <hr style="border-color:var(--spa-border-soft)">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('productos.index') }}" class="btn btn-spa-secondary">Cancelar</a>
            <button class="btn btn-spa-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </div>
</form>
@endsection
