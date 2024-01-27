<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacaoDiretoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diretoria_informacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('contato_presidente')->nullable();
            $table->string('contato_vice_presidente')->nullable();
            $table->string('contato_primeiro_secretario')->nullable();
            $table->string('contato_segundo_secretario')->nullable();
            $table->string('contato_secretario_executivo')->nullable();
            $table->string('contato_tesoureiro')->nullable();
            $table->string('contato_secretario_causas')->nullable();

            $table->string('path_presidente')->nullable();
            $table->string('path_vice_presidente')->nullable();
            $table->string('path_primeiro_secretario')->nullable();
            $table->string('path_segundo_secretario')->nullable();
            $table->string('path_secretario_executivo')->nullable();
            $table->string('path_tesoureiro')->nullable();
            $table->string('path_secretario_causas')->nullable();

            $table->uuid('diretoria_id')->nullable();

            $table->timestamps();

            $table->foreign('diretoria_id')->references('id')->on('diretorias')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diretoria_informacoes');
    }
}
