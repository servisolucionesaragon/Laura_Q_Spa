<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->string('color_menu_activo', 10)->nullable()->after('color_sidebar_texto');
            $table->string('color_fondo', 10)->nullable()->after('color_menu_activo');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->dropColumn(['color_menu_activo', 'color_fondo']);
        });
    }
};
