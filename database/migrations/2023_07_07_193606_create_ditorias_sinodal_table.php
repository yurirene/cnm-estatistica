<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDitoriasSinodalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diretorias_sinodal', function (Blueprint $table) {
            $table->id();
            $table->string('presidente')->nullable();
            $table->string('path_presidente')->nullable();
            $table->string('contato_presidente')->nullable();
            $table->string('vice_presidente')->nullable();
            $table->string('path_vice_presidente')->nullable();
            $table->string('contato_vice_presidente')->nullable();
            $table->string('secretario_executivo')->nullable();
            $table->string('path_secretario_executivo')->nullable();
            $table->string('contato_secretario_executivo')->nullable();
            $table->string('primeiro_secretario')->nullable();
            $table->string('path_primeiro_secretario')->nullable();
            $table->string('contato_primeiro_secretario')->nullable();
            $table->string('segundo_secretario')->nullable();
            $table->string('path_segundo_secretario')->nullable();
            $table->string('contato_segundo_secretario')->nullable();
            $table->string('tesoureiro')->nullable();
            $table->string('path_tesoureiro')->nullable();
            $table->string('contato_tesoureiro')->nullable();
            $table->string('secretario_sinodal')->nullable();
            $table->string('path_secretario_sinodal')->nullable();
            $table->string('contato_secretario_sinodal')->nullable();
            $table->uuid('sinodal_id');

            $table->timestamps();

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
        Schema::dropIfExists('diretorias_sinodal');
    }
}
