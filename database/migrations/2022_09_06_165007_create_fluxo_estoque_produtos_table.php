<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFluxoEstoqueProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fluxo_estoque_produtos', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('tipo')->default(0);
            $table->integer('quantidade');
            $table->string('observacao')->nullable();
            $table->bigInteger('produto_id')->unsigned();
            $table->timestamps();

            $table->foreign('produto_id')->references('id')->on('produtos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fluxo_estoque_produtos');
    }
}
