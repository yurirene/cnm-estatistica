<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularioComplementarFederacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formulario_complementar_federacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('formulario')->nullable();
            $table->json('referencias')->nullable();
            $table->json('configuracoes')->nullable();
            $table->boolean('status')->default(false);
            $table->uuid('federacao_id');
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
        Schema::dropIfExists('formulario_complementar_federacoes');
    }
}
