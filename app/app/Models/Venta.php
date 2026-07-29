<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'numero', 'cliente_id', 'user_id', 'cita_id', 'fecha',
        'subtotal', 'descuento', 'impuesto', 'total',
        'metodo_pago', 'estado', 'notas',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function items()
    {
        return $this->hasMany(VentaItem::class);
    }

    public function pagos()
    {
        return $this->hasMany(VentaPago::class);
    }

    public static function generarNumero(): string
    {
        $prefijo = 'V-' . date('Ym');
        $ult = self::where('numero', 'like', $prefijo . '%')->orderByDesc('id')->first();
        $consec = 1;
        if ($ult) {
            $consec = ((int) substr($ult->numero, strlen($prefijo) + 1)) + 1;
        }
        return $prefijo . '-' . str_pad((string)$consec, 4, '0', STR_PAD_LEFT);
    }
}
