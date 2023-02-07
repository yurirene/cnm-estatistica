<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFluxoCaixasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos_fluxo_caixa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('descricao');
            $table->date('data_lancamento');
            $table->float('valor')->default(0);
            $table->tinyInteger('tipo');
            $table->string('comprovante')->nullable();
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
        Schema::dropIfExists('produtos_fluxo_caixa');
    }
}
