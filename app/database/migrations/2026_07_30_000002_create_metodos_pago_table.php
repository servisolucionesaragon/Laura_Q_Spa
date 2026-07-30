<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('metodos_pago')->insert([
            ['nombre' => 'efectivo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'tarjeta', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'transferencia', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'mixto', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'otro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ventas.metodo_pago y venta_pagos.metodo eran ENUM de fábrica (lista fija).
        // Se convierten a VARCHAR vía SQL crudo (Blueprint::change() requiere doctrine/dbal,
        // que no está instalado) para que acepten cualquier valor del catálogo nuevo.
        DB::statement("ALTER TABLE ventas MODIFY metodo_pago VARCHAR(50) NOT NULL DEFAULT 'efectivo'");
        DB::statement("ALTER TABLE venta_pagos MODIFY metodo VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ventas MODIFY metodo_pago ENUM('efectivo','tarjeta','transferencia','mixto','otro') NOT NULL DEFAULT 'efectivo'");
        DB::statement("ALTER TABLE venta_pagos MODIFY metodo ENUM('efectivo','tarjeta','transferencia','otro') NOT NULL");
        Schema::dropIfExists('metodos_pago');
    }
};
