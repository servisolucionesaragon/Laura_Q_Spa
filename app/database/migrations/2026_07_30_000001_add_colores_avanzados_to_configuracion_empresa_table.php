<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->string('color_accent', 10)->nullable()->after('color_secundario');
            $table->string('color_sidebar_fondo', 10)->nullable()->after('color_accent');
            $table->string('color_sidebar_texto', 10)->nullable()->after('color_sidebar_fondo');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->dropColumn(['color_accent', 'color_sidebar_fondo', 'color_sidebar_texto']);
        });
    }
};
