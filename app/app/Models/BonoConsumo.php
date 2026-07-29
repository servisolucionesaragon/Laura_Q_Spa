<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonoConsumo extends Model
{
    protected $fillable = ['bono_id', 'cita_id', 'user_id', 'fecha', 'descripcion'];

    protected $casts = ['fecha' => 'date'];

    public function bono()
    {
        return $this->belongsTo(Bono::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
