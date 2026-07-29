<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'contacto', 'telefono', 'email', 'direccion', 'notas', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }
}
