<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('identificador')->nullable();
            $table->string('mensagem');
            $table->string('keywords')->nullable();
            $table->uuid('resposta_de')->nullable();
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
        Schema::dropIfExists('bot_messages');
    }
}
