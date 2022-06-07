<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulariosFederacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formularios_federacao_v1', function (Blueprint $table) {
            $table->id();
            $table->year('ano_referencia');
            $table->json('aci')->nullable();
            $table->json('perfil')->nullable();
            $table->json('deficiencias')->nullable();
            $table->json('estado_civil')->nullable();
            $table->json('escolaridade')->nullable();
            $table->json('programacoes_locais')->nullable();
            $table->json('programacoes')->nullable();
            $table->json('estrutura')->nullable();
            $table->uuid('federacao_id');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('federacao_id')->references('id')->on('federacoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('formularios_federacao_v1');
    }
}
