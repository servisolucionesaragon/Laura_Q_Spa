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

    protected function nombreEmpresa(): string
    {
        return ConfiguracionEmpresa::first()?->nombre_empresa ?? 'nuestro spa';
    }

    protected function fechaHoraTexto(): string
    {
        $fecha = $this->fecha->locale('es')->isoFormat('D [de] MMMM');
        $hora = \Carbon\Carbon::parse($this->hora_inicio)->format('H:i');
        return "{$fecha} a las {$hora}";
    }

    public function mensajeRecordatorio(): string
    {
        return "Hola {$this->cliente?->nombre}, te recordamos tu cita en *{$this->nombreEmpresa()}* el {$this->fechaHoraTexto()}. ¡Te esperamos!";
    }

    public function mensajeCambioEstado(string $estado): string
    {
        $nombre = $this->cliente?->nombre;
        $empresa = $this->nombreEmpresa();
        $cuando = $this->fechaHoraTexto();

        return match ($estado) {
            'confirmada' => "Hola {$nombre}, tu cita en *{$empresa}* para el {$cuando} ha sido *confirmada*. ¡Te esperamos!",
            'cancelada'  => "Hola {$nombre}, lamentamos informarte que tu cita del {$cuando} en *{$empresa}* fue *cancelada*. Escríbenos si deseas reagendar.",
            'realizada'  => "Hola {$nombre}, ¡gracias por tu visita a *{$empresa}*! Esperamos que hayas disfrutado tu cita.",
            'no_show'    => "Hola {$nombre}, notamos que no pudiste asistir a tu cita del {$cuando} en *{$empresa}*. Escríbenos si deseas reagendar.",
            default      => "Hola {$nombre}, tu cita en *{$empresa}* del {$cuando} cambió de estado a: {$estado}.",
        };
    }
}
