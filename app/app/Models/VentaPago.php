<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaPago extends Model
{
    protected $table = 'venta_pagos';

    protected $fillable = ['venta_id', 'metodo', 'monto', 'referencia'];

    protected $casts = ['monto' => 'decimal:2'];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
