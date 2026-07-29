<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabina extends Model
{
    use HasFactory;

    protected $table = 'cabinas';

    protected $fillable = ['nombre', 'descripcion', 'color', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function scopeActivas($q)
    {
        return $q->where('activo', true);
    }
}
