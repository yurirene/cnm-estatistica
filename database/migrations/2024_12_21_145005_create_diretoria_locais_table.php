<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiretoriaLocaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diretorias_local', function (Blueprint $table) {
            $table->id();
            $table->string('presidente')->nullable();
            $table->string('path_presidente')->nullable();
            $table->string('contato_presidente')->nullable();
            $table->string('vice_presidente')->nullable();
            $table->string('path_vice_presidente')->nullable();
            $table->string('contato_vice_presidente')->nullable();
            $table->string('primeiro_secretario')->nullable();
            $table->string('path_primeiro_secretario')->nullable();
            $table->string('contato_primeiro_secretario')->nullable();
            $table->string('segundo_secretario')->nullable();
            $table->string('path_segundo_secretario')->nullable();
            $table->string('contato_segundo_secretario')->nullable();
            $table->string('tesoureiro')->nullable();
            $table->string('path_tesoureiro')->nullable();
            $table->string('contato_tesoureiro')->nullable();
            $table->string('conselheiro')->nullable();
            $table->string('path_conselheiro')->nullable();
            $table->string('contato_conselheiro')->nullable();
            $table->uuid('local_id');
            $table->json('secretarios')->nullable();

            $table->timestamps();

            $table->foreign('local_id')->references('id')->on('locais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diretorias_local');
    }
}
