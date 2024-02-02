<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLancamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return voiddatabase/migrations/2024_02_01_202811_create_app_federacao_table.php
     */
    public function up()
    {
        Schema::create('tesouraria_lancamentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('descricao');
            $table->date('data_lancamento');
            $table->float('valor')->default(0);
            $table->tinyInteger('tipo');
            $table->string('comprovante')->nullable();
            $table->uuid('categoria_id')->nullable();

            $table->uuid('sinodal_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->uuid('local_id')->nullable();

            $table->timestamps();

            $table->foreign('sinodal_id')->references('id')->on('sinodais')->onDelete('cascade');
            $table->foreign('federacao_id')->references('id')->on('federacoes')->onDelete('cascade');
            $table->foreign('local_id')->references('id')->on('locais')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('tesouraria_categorias')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tesouraria_lancamentos');
    }
}
