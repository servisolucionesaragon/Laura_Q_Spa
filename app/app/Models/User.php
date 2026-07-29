<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'telefono',
        'avatar',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function tieneRol(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->rol, $roles, true);
        }
        return $this->rol === $roles;
    }

    public function getRolNombreAttribute(): string
    {
        return match ($this->rol) {
            'admin' => 'Administrador',
            'recepcionista' => 'Recepcionista',
            'profesional' => 'Profesional',
            'cajero' => 'Cajero',
            default => ucfirst((string) $this->rol),
        };
    }
}
