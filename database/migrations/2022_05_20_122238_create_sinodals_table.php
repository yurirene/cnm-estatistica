<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSinodalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sinodais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('sigla');
            $table->date('data_organizacao')->nullable();
            $table->string('sinodo')->nullable();
            $table->string('midias_sociais')->nullable();
            $table->bigInteger('regiao_id')->unsigned();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('regiao_id')->references('id')->on('regioes');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sinodais');
    }
}
