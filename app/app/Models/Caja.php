<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'user_id', 'fecha', 'monto_apertura', 'monto_cierre', 'monto_esperado',
        'diferencia', 'estado', 'notas_apertura', 'notas_cierre', 'abierta_en', 'cerrada_en',
    ];

    protected $casts = [
        'fecha'          => 'date',
        'monto_apertura' => 'decimal:2',
        'monto_cierre'   => 'decimal:2',
        'monto_esperado' => 'decimal:2',
        'diferencia'     => 'decimal:2',
        'abierta_en'     => 'datetime',
        'cerrada_en'     => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class)->latest();
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public static function abiertaActual(): ?self
    {
        return static::where('estado', 'abierta')->latest('abierta_en')->first();
    }

    public function totalVentasEfectivo(): float
    {
        return (float) $this->ventas()
            ->where('metodo_pago', 'efectivo')
            ->where('estado', '!=', 'anulada')
            ->sum('total');
    }

    public function totalIngresos(): float
    {
        return (float) $this->movimientos()->where('tipo', 'ingreso')->sum('monto');
    }

    public function totalEgresos(): float
    {
        return (float) $this->movimientos()->where('tipo', 'egreso')->sum('monto');
    }

    public function montoEsperadoCalculado(): float
    {
        return round(
            (float) $this->monto_apertura
            + $this->totalVentasEfectivo()
            + $this->totalIngresos()
            - $this->totalEgresos(),
            2
        );
    }
}
