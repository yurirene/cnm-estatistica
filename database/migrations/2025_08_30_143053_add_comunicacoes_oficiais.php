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
        Schema::table('comissao_executiva_reunioes', function (Blueprint $table) {
            $table->boolean('diretoria')->default(false);
            $table->boolean('relatorio_estatistico')->default(false);
        });
        

        Schema::table('diretorias_sinodal', function (Blueprint $table) {
            $table->uuid('reuniao_notificada')->nullable();
        });
        
        Schema::table('diretorias_federacao', function (Blueprint $table) {
            $table->uuid('reuniao_notificada')->nullable();
        });

        Schema::table('diretorias_local', function (Blueprint $table) {
            $table->uuid('reuniao_notificada')->nullable();
        });
        
        Schema::table('formularios_sinodal_v1', function (Blueprint $table) {
            $table->uuid('reuniao_notificada')->nullable();
        });

        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->uuid('reuniao_notificada')->nullable();
        });

        Schema::table('formularios_local_v1', function (Blueprint $table) {
            $table->uuid('reuniao_notificada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comissao_executiva_reunioes', function (Blueprint $table) {
            $table->dropColumn('diretoria');
            $table->dropColumn('relatorio_estatistico');
        });

        Schema::table('diretorias_sinodal', function (Blueprint $table) {
            $table->dropColumn('reuniao_notificada');
        });

        Schema::table('diretorias_federacao', function (Blueprint $table) {
            $table->dropColumn('reuniao_notificada');
        });

        Schema::table('diretorias_local', function (Blueprint $table) {
            $table->dropColumn('reuniao_notificada');
        });

        Schema::table('formularios_sinodal_v1', function (Blueprint $table) {
            $table->dropColumn('reuniao_notificada');
        });

        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->dropColumn('reuniao_notificada');
        });

        Schema::table('formularios_local_v1', function (Blueprint $table) {
            $table->dropColumn('reuniao_notificada');
        });
    }
};
