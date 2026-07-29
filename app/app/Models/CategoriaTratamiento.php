<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaTratamiento extends Model
{
    protected $table = 'categorias_tratamientos';

    protected $fillable = ['nombre', 'descripcion', 'color', 'icono', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'categoria_id');
    }

    public function scopeActivas($q)
    {
        return $q->where('activo', true);
    }
}
