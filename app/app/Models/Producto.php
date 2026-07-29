<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'proveedor_id', 'codigo', 'nombre', 'descripcion',
        'precio_compra', 'precio_venta', 'stock_actual', 'stock_minimo',
        'unidad', 'para_venta', 'para_uso_interno', 'activo',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta'  => 'decimal:2',
        'stock_actual'  => 'integer',
        'stock_minimo'  => 'integer',
        'para_venta'    => 'boolean',
        'para_uso_interno' => 'boolean',
        'activo'        => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoStock::class)->orderByDesc('id');
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function getStockSinAttribute(): bool
    {
        return $this->stock_actual <= 0;
    }

    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }

    public function scopeParaVenta($q)
    {
        return $q->where('para_venta', true);
    }
}
