<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularioComplementarSinodalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formulario_complementar_sinodais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('formulario')->nullable();
            $table->json('referencias')->nullable();
            $table->json('configuracoes')->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('formulario_complementar_sinodais');
    }
}
