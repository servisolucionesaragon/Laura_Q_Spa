<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bono extends Model
{
    protected $fillable = [
        'codigo', 'cliente_id', 'plantilla_id', 'nombre',
        'sesiones_total', 'sesiones_usadas', 'fecha_compra',
        'fecha_vencimiento', 'precio_pagado', 'estado', 'notas',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_vencimiento' => 'date',
        'precio_pagado' => 'decimal:2',
        'sesiones_total' => 'integer',
        'sesiones_usadas' => 'integer',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function plantilla()
    {
        return $this->belongsTo(BonoPlantilla::class, 'plantilla_id');
    }

    public function consumos()
    {
        return $this->hasMany(BonoConsumo::class)->orderByDesc('fecha');
    }

    public function getSesionesRestantesAttribute(): int
    {
        return max(0, $this->sesiones_total - $this->sesiones_usadas);
    }

    public function getEsActivoAttribute(): bool
    {
        return $this->estado === 'activo' && $this->sesiones_restantes > 0;
    }

    public function actualizarEstado(): void
    {
        if ($this->fecha_vencimiento && $this->fecha_vencimiento->isPast()) {
            $this->estado = 'vencido';
        } elseif ($this->sesiones_usadas >= $this->sesiones_total) {
            $this->estado = 'agotado';
        } else {
            $this->estado = 'activo';
        }
        $this->save();
    }

    public function scopeActivos($q)
    {
        return $q->where('estado', 'activo');
    }
}
