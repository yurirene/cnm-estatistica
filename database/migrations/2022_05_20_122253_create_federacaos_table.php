<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFederacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('federacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('sigla');
            $table->string('presbiterio')->nullable();
            $table->string('midias_sociais')->nullable();
            $table->date('data_organizacao')->nullable();
            $table->bigInteger('estado_id')->unsigned();
            $table->bigInteger('regiao_id')->unsigned();
            $table->uuid('sinodal_id');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estado_id')->references('id')->on('estados');
            $table->foreign('regiao_id')->references('id')->on('regioes');
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
        Schema::dropIfExists('federacoes');
    }
}
