<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('formulario_complementar_sinodais', function (Blueprint $table) {
            $table->year('ano')->default('2025');
        });

        Schema::table('formulario_complementar_federacoes', function (Blueprint $table) {
            $table->year('ano')->default('2025');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formulario_complementar_sinodais', function (Blueprint $table) {
            $table->dropColumn('ano');
        });
        Schema::table('formulario_complementar_federacoes', function (Blueprint $table) {
            $table->dropColumn('ano');
        });
    }
};
