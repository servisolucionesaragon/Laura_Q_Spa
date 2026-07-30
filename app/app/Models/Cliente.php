<?php

namespace App\Models;

use App\Traits\TieneWhatsapp;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use TieneWhatsapp;

    protected $fillable = [
        'nombre', 'apellido', 'email', 'telefono', 'fecha_nacimiento',
        'genero', 'direccion', 'ciudad', 'documento', 'alergias',
        'notas', 'como_nos_conocio', 'acepta_marketing', 'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'acepta_marketing' => 'boolean',
        'activo' => 'boolean',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class)->orderByDesc('fecha')->orderByDesc('hora_inicio');
    }

    public function bonos()
    {
        return $this->hasMany(Bono::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class)->orderByDesc('fecha');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }

    public function scopeBuscar($q, ?string $busqueda)
    {
        if (! $busqueda) return $q;
        return $q->where(function ($w) use ($busqueda) {
            $w->where('nombre', 'like', "%{$busqueda}%")
              ->orWhere('apellido', 'like', "%{$busqueda}%")
              ->orWhere('telefono', 'like', "%{$busqueda}%")
              ->orWhere('email', 'like', "%{$busqueda}%");
        });
    }

    /**
     * Cumple años dentro del mes actual (sin importar el año).
     * Nombre elegido a propósito para no colisionar con el scope de abajo:
     * PHP no distingue mayúsculas en nombres de método
     * (cumpleAnioEsteMes vs scopeConCumpleanioEsteMes no chocan).
     */
    public function cumpleAnioEsteMes(): bool
    {
        return $this->fecha_nacimiento
            && (int) $this->fecha_nacimiento->format('m') === (int) now()->format('m');
    }

    public function scopeConCumpleanioEsteMes($q)
    {
        return $q->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', now()->month);
    }
}
