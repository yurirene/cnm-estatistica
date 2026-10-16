<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscipuladoAndOrganizacaoToFormulariosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formularios_local_v1', function (Blueprint $table) {
            $table->json('discipulado')->nullable()->after('programacoes');
            $table->json('organizacao')->nullable()->after('discipulado');
        });

        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->json('discipulado')->nullable()->after('programacoes');
            $table->json('organizacao')->nullable()->after('discipulado');
        });

        Schema::table('formularios_sinodal_v1', function (Blueprint $table) {
            $table->json('discipulado')->nullable()->after('programacoes');
            $table->json('organizacao')->nullable()->after('discipulado');
        });

        Schema::table('estatistica_gerais', function (Blueprint $table) {
            $table->json('discipulado')->nullable()->after('escolaridade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formularios_local_v1', function (Blueprint $table) {
            $table->dropColumn(['discipulado', 'organizacao']);
        });

        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->dropColumn(['discipulado', 'organizacao']);
        });

        Schema::table('formularios_sinodal_v1', function (Blueprint $table) {
            $table->dropColumn(['discipulado', 'organizacao']);
        });

        Schema::table('estatistica_gerais', function (Blueprint $table) {
            $table->dropColumn('discipulado');
        });
    }
}
