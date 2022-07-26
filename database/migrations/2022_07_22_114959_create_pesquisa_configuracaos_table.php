<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesquisaConfiguracaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesquisa_configuracoes', function (Blueprint $table) {
            $table->id();
            $table->uuid('pesquisa_id');
            $table->json('configuracao')->nullable();
            $table->timestamps();

            $table->foreign('pesquisa_id')->references('id')->on('pesquisas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pesquisa_configuracoes');
    }
}
