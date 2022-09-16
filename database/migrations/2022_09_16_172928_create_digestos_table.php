<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDigestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digestos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->longText('texto');
            $table->year('ano');
            $table->bigInteger('tipo_reuniao_id')->unsigned();
            $table->string('path');

            $table->timestamps();

            $table->foreign('tipo_reuniao_id')->references('id')->on('tipo_reunioes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digestos');
    }
}
