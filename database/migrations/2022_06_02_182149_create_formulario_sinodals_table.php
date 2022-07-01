<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularioSinodalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formularios_sinodal_v1', function (Blueprint $table) {
            $table->id();
            $table->year('ano_referencia');
            $table->json('aci')->nullable();
            $table->json('perfil')->nullable();
            $table->json('deficiencias')->nullable();
            $table->json('estado_civil')->nullable();
            $table->json('escolaridade')->nullable();
            $table->json('programacoes_locais')->nullable();
            $table->json('programacoes_federacoes')->nullable();
            $table->json('programacoes')->nullable();
            $table->json('estrutura')->nullable();
            $table->uuid('sinodal_id');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sinodal_id')->references('id')->on('sinodais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('formulario_sinodals');
    }
}
