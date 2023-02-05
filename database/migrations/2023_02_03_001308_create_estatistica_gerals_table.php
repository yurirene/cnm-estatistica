<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstatisticaGeralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estatistica_gerais', function (Blueprint $table) {
            $table->id();

            $table->year('ano_referencia');
            $table->json('aci')->nullable();
            $table->json('perfil')->nullable();
            $table->json('deficiencias')->nullable();
            $table->json('estado_civil')->nullable();
            $table->json('escolaridade')->nullable();
            $table->json('programacoes_locais')->nullable();
            $table->json('programacoes_federacoes')->nullable();
            $table->json('programacoes_sinodais')->nullable();
            $table->json('estrutura')->nullable();
            $table->json('abrangencia')->nullable();
            $table->float('qualidade')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estatistica_gerais');
    }
}
