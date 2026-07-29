<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa')->default('TPV Estética y SPA');
            $table->string('razon_social')->nullable();
            $table->string('nit_rfc', 50)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 100)->default('Guatemala');
            $table->string('logo')->nullable();
            $table->string('simbolo_moneda', 5)->default('Q');
            $table->string('codigo_moneda', 5)->default('GTQ');
            $table->string('formato_moneda', 30)->default('symbol_amount'); // symbol_amount | amount_symbol
            $table->decimal('impuesto_porcentaje', 5, 2)->default(12.00);
            $table->string('nombre_impuesto', 30)->default('IVA');
            $table->boolean('impuesto_incluido')->default(true);
            $table->string('zona_horaria', 50)->default('America/Guatemala');
            $table->string('color_primario', 10)->default('#d4a5c0');
            $table->string('color_secundario', 10)->default('#8b6f8e');
            $table->time('hora_apertura')->default('09:00:00');
            $table->time('hora_cierre')->default('20:00:00');
            $table->json('dias_laborales')->nullable();
            $table->integer('intervalo_citas_min')->default(30);
            $table->text('mensaje_recibo')->nullable();
            $table->text('terminos_condiciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_empresa');
    }
};
