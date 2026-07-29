<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionEmpresa extends Model
{
    protected $table = 'configuracion_empresa';

    protected $fillable = [
        'nombre_empresa',
        'razon_social',
        'nit_rfc',
        'telefono',
        'email',
        'sitio_web',
        'direccion',
        'ciudad',
        'pais',
        'logo',
        'simbolo_moneda',
        'codigo_moneda',
        'formato_moneda',
        'impuesto_porcentaje',
        'nombre_impuesto',
        'impuesto_incluido',
        'zona_horaria',
        'color_primario',
        'color_secundario',
        'hora_apertura',
        'hora_cierre',
        'dias_laborales',
        'intervalo_citas_min',
        'mensaje_recibo',
        'terminos_condiciones',
    ];

    protected $casts = [
        'impuesto_porcentaje' => 'decimal:2',
        'impuesto_incluido' => 'boolean',
        'dias_laborales' => 'array',
    ];

    /**
     * Devuelve la única fila de configuración (la crea si no existe).
     */
    public static function obtener(): self
    {
        return self::firstOrCreate([], [
            'nombre_empresa' => 'TPV Estética y SPA',
            'simbolo_moneda' => 'Q',
            'codigo_moneda' => 'GTQ',
            'impuesto_porcentaje' => 12.00,
            'nombre_impuesto' => 'IVA',
            'pais' => 'Guatemala',
            'zona_horaria' => 'America/Guatemala',
            'color_primario' => '#d4a5c0',
            'color_secundario' => '#8b6f8e',
            'hora_apertura' => '09:00:00',
            'hora_cierre' => '20:00:00',
            'intervalo_citas_min' => 30,
            'dias_laborales' => ['lun', 'mar', 'mie', 'jue', 'vie', 'sab'],
        ]);
    }

    public function formatearMoneda(float|int|string $monto): string
    {
        $monto = number_format((float) $monto, 2, '.', ',');
        return $this->formato_moneda === 'amount_symbol'
            ? "{$monto} {$this->simbolo_moneda}"
            : "{$this->simbolo_moneda} {$monto}";
    }

    public function logoUrl(): ?string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : null;
    }
}
