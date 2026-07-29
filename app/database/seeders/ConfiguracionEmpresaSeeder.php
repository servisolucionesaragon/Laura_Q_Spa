<?php

namespace Database\Seeders;

use App\Models\ConfiguracionEmpresa;
use Illuminate\Database\Seeder;

class ConfiguracionEmpresaSeeder extends Seeder
{
    public function run(): void
    {
        ConfiguracionEmpresa::firstOrCreate([], [
            'nombre_empresa'      => 'Bella Estética & SPA',
            'razon_social'        => 'Bella Estética y SPA, S.A.',
            'nit_rfc'             => '123456-7',
            'telefono'            => '+502 0000-0000',
            'email'               => 'contacto@bellaspa.com',
            'sitio_web'           => 'https://bellaspa.com',
            'direccion'           => '6a Avenida 10-25, Zona 10',
            'ciudad'              => 'Ciudad de Guatemala',
            'pais'                => 'Guatemala',
            'simbolo_moneda'      => 'Q',
            'codigo_moneda'       => 'GTQ',
            'formato_moneda'      => 'symbol_amount',
            'impuesto_porcentaje' => 12.00,
            'nombre_impuesto'     => 'IVA',
            'impuesto_incluido'   => true,
            'zona_horaria'        => 'America/Guatemala',
            'color_primario'      => '#d4a5c0',
            'color_secundario'    => '#8b6f8e',
            'hora_apertura'       => '09:00:00',
            'hora_cierre'         => '20:00:00',
            'dias_laborales'      => ['lun', 'mar', 'mie', 'jue', 'vie', 'sab'],
            'intervalo_citas_min' => 30,
            'mensaje_recibo'      => '¡Gracias por tu visita! Te esperamos pronto.',
        ]);
    }
}
