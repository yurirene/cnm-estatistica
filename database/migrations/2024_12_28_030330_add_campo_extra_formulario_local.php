<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoExtraFormularioLocal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formularios_local_v1', function (Blueprint $table) {
            $table->json('campo_extra_federacao')->nullable()->after('programacoes');
            $table->json('campo_extra_sinodal')->nullable()->after('programacoes');
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
            $table->dropColumn('campo_extra_federacao');
            $table->dropColumn('campo_extra_sinodal');
        });
    }
}
