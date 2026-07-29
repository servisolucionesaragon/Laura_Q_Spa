<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonos_plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->integer('sesiones_total')->default(1);
            $table->integer('validez_dias')->default(180);
            $table->foreignId('tratamiento_id')->nullable()->constrained('tratamientos')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('bonos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('plantilla_id')->nullable()->constrained('bonos_plantillas')->nullOnDelete();
            $table->string('nombre');
            $table->integer('sesiones_total');
            $table->integer('sesiones_usadas')->default(0);
            $table->date('fecha_compra');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('precio_pagado', 10, 2);
            $table->enum('estado', ['activo', 'agotado', 'vencido', 'cancelado'])->default('activo');
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        Schema::create('bono_consumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bono_id')->constrained('bonos')->cascadeOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bono_consumos');
        Schema::dropIfExists('bonos');
        Schema::dropIfExists('bonos_plantillas');
    }
};
