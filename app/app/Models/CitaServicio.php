<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaServicio extends Model
{
    protected $table = 'cita_servicios';

    protected $fillable = ['cita_id', 'tratamiento_id', 'descripcion', 'duracion_min', 'precio'];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_min' => 'integer',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class);
    }
}
