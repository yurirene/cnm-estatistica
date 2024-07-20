<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tesouraria_categorias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');

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
        Schema::dropIfExists('tesouraria_categorias');
    }
}
