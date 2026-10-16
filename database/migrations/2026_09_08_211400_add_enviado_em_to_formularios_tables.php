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
        Schema::table('formularios_local_v1', function (Blueprint $table) {
            $table->timestamp('enviado_em')->nullable()->after('reuniao_notificada');
        });

        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->timestamp('enviado_em')->nullable()->after('reuniao_notificada');
        });

        Schema::table('formularios_sinodal_v1', function (Blueprint $table) {
            $table->timestamp('enviado_em')->nullable()->after('reuniao_notificada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formularios_local_v1', function (Blueprint $table) {
            $table->dropColumn('enviado_em');
        });

        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->dropColumn('enviado_em');
        });

        Schema::table('formularios_sinodal_v1', function (Blueprint $table) {
            $table->dropColumn('enviado_em');
        });
    }
};
