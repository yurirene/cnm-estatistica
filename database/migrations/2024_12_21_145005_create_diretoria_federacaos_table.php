<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiretoriaFederacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diretorias_federacao', function (Blueprint $table) {
            $table->id();
            $table->string('presidente')->nullable();
            $table->string('path_presidente')->nullable();
            $table->string('contato_presidente')->nullable();
            $table->string('vice_presidente')->nullable();
            $table->string('path_vice_presidente')->nullable();
            $table->string('contato_vice_presidente')->nullable();
            $table->string('secretaria_executiva')->nullable();
            $table->string('path_secretaria_executiva')->nullable();
            $table->string('contato_secretaria_executiva')->nullable();
            $table->string('primeiro_secretario')->nullable();
            $table->string('path_primeiro_secretario')->nullable();
            $table->string('contato_primeiro_secretario')->nullable();
            $table->string('segundo_secretario')->nullable();
            $table->string('path_segundo_secretario')->nullable();
            $table->string('contato_segundo_secretario')->nullable();
            $table->string('tesoureiro')->nullable();
            $table->string('path_tesoureiro')->nullable();
            $table->string('contato_tesoureiro')->nullable();
            $table->string('secretario_presbiterial')->nullable();
            $table->string('path_secretario_presbiterial')->nullable();
            $table->string('contato_secretario_presbiterial')->nullable();
            $table->uuid('federacao_id');
            $table->json('secretarios')->nullable();

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
        Schema::dropIfExists('diretorias_federacao');
    }
}
