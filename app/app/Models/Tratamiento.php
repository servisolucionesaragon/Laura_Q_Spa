<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'descripcion', 'duracion_min',
        'precio', 'comision_porcentaje', 'requiere_cabina', 'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'comision_porcentaje' => 'decimal:2',
        'duracion_min' => 'integer',
        'requiere_cabina' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaTratamiento::class, 'categoria_id');
    }

    public function citaServicios()
    {
        return $this->hasMany(CitaServicio::class);
    }

    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }
}
