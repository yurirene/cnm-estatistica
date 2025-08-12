<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoExtraFormularioFederacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->json('campo_extra_sinodal')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formularios_federacao_v1', function (Blueprint $table) {
            $table->dropColumn('campo_extra_sinodal');
        });
    }
}
