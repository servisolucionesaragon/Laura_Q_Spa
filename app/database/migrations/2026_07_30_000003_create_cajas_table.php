<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha');
            $table->decimal('monto_apertura', 10, 2)->default(0);
            $table->decimal('monto_cierre', 10, 2)->nullable();
            $table->decimal('monto_esperado', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->nullable();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('notas_apertura')->nullable();
            $table->text('notas_cierre')->nullable();
            $table->dateTime('abierta_en');
            $table->dateTime('cerrada_en')->nullable();
            $table->timestamps();

            $table->index('fecha');
            $table->index('estado');
        });

        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->string('concepto', 191);
            $table->decimal('monto', 10, 2);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('caja_id')->nullable()->after('user_id')
                ->constrained('cajas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('caja_id');
        });
        Schema::dropIfExists('movimientos_caja');
        Schema::dropIfExists('cajas');
    }
};
