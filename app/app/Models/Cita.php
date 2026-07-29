<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'cliente_id', 'profesional_id', 'cabina_id', 'fecha',
        'hora_inicio', 'hora_fin', 'estado', 'total', 'notas', 'creado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function cabina()
    {
        return $this->belongsTo(Cabina::class);
    }

    public function servicios()
    {
        return $this->hasMany(CitaServicio::class);
    }

    public function creadaPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'pendiente'  => 'warning',
            'confirmada' => 'info',
            'realizada'  => 'success',
            'cancelada'  => 'danger',
            'no_show'    => 'danger',
            default      => '',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'pendiente'  => 'Pendiente',
            'confirmada' => 'Confirmada',
            'realizada'  => 'Realizada',
            'cancelada'  => 'Cancelada',
            'no_show'    => 'No asistió',
            default      => ucfirst((string) $this->estado),
        };
    }

    public function recalcularTotal(): void
    {
        $this->total = $this->servicios()->sum('precio');
        $this->save();
    }

    public function recalcularDuracion(): void
    {
        $duracion = $this->servicios()->sum('duracion_min') ?: 30;
        $this->hora_fin = \Carbon\Carbon::parse($this->hora_inicio)->addMinutes($duracion)->format('H:i:s');
        $this->save();
    }
}
