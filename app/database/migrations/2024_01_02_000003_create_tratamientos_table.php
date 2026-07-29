<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_tratamientos')->nullOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('duracion_min')->default(30);
            $table->decimal('precio', 10, 2)->default(0);
            $table->decimal('comision_porcentaje', 5, 2)->default(0);
            $table->boolean('requiere_cabina')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
