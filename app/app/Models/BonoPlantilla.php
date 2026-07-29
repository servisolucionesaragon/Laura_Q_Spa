<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonoPlantilla extends Model
{
    protected $table = 'bonos_plantillas';

    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'sesiones_total',
        'validez_dias', 'tratamiento_id', 'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'sesiones_total' => 'integer',
        'validez_dias' => 'integer',
        'activo' => 'boolean',
    ];

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class);
    }

    public function bonos()
    {
        return $this->hasMany(Bono::class, 'plantilla_id');
    }

    public function scopeActivas($q)
    {
        return $q->where('activo', true);
    }
}
