<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignacaoProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignacao_produtos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('produto_id')->unsigned();
            $table->uuid('user_id');
            $table->integer('quantidade_consignada');
            $table->integer('quantidade_retornada');
            $table->timestamps();

            $table->foreign('produto_id')->references('id')->on('produtos');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consignacao_produtos');
    }
}
