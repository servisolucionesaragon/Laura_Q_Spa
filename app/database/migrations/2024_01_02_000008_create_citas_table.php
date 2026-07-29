<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('profesional_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cabina_id')->nullable()->constrained('cabinas')->nullOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('estado', ['pendiente', 'confirmada', 'realizada', 'cancelada', 'no_show'])
                  ->default('pendiente');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fecha', 'hora_inicio']);
            $table->index(['profesional_id', 'fecha']);
            $table->index(['cabina_id', 'fecha']);
        });

        Schema::create('cita_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->cascadeOnDelete();
            $table->foreignId('tratamiento_id')->nullable()->constrained('tratamientos')->nullOnDelete();
            $table->string('descripcion');
            $table->integer('duracion_min')->default(30);
            $table->decimal('precio', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita_servicios');
        Schema::dropIfExists('citas');
    }
};
