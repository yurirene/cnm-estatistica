<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotEnviosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_envios', function (Blueprint $table) {
            $table->id();
            $table->uuid('bot_cliente_id');
            $table->uuid('mensagem_servidor')->nullable();
            $table->string('mensagem_cliente')->nullable();
            $table->timestamps();

            $table->foreign('mensagem_servidor')->references('id')->on('bot_messages');
            $table->foreign('bot_cliente_id')->references('id')->on('bot_clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_envios');
    }
}
