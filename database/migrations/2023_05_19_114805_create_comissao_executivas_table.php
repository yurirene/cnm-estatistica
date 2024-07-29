<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComissaoExecutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissao_executiva_reunioes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->year('ano');
            $table->string('local');
            $table->tinyInteger('aberto');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('comissao_executiva_reunioes');
    }
}
