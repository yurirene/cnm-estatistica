<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiretoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diretorias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('presidente')->nullable();
            $table->string('vice_presidente')->nullable();
            $table->string('primeiro_secretario')->nullable();
            $table->string('segundo_secretario')->nullable();
            $table->string('secretario_executivo')->nullable();
            $table->string('tesoureiro')->nullable();
            $table->string('secretario_causas')->nullable();

            $table->uuid('sinodal_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->uuid('local_id')->nullable();

            $table->timestamps();

            $table->foreign('sinodal_id')->references('id')->on('sinodais')->onDelete('cascade');
            $table->foreign('federacao_id')->references('id')->on('federacoes')->onDelete('cascade');
            $table->foreign('local_id')->references('id')->on('locais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diretorias');
    }
}
