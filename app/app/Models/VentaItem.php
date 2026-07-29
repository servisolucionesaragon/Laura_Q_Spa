<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaItem extends Model
{
    protected $table = 'venta_items';

    protected $fillable = [
        'venta_id', 'tipo', 'referencia_id', 'profesional_id',
        'descripcion', 'cantidad', 'precio_unitario', 'descuento', 'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }
}
