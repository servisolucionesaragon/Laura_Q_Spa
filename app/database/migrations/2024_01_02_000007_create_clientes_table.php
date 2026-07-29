<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['F', 'M', 'O'])->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('documento', 50)->nullable();
            $table->text('alergias')->nullable();
            $table->text('notas')->nullable();
            $table->string('como_nos_conocio', 100)->nullable();
            $table->boolean('acepta_marketing')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['nombre', 'apellido']);
            $table->index('telefono');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
