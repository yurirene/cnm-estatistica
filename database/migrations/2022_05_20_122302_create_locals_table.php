<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->date('data_organizacao')->nullable();
            $table->string('midias_sociais')->nullable();
            $table->bigInteger('estado_id')->unsigned();
            $table->bigInteger('regiao_id')->unsigned();
            $table->uuid('federacao_id');
            $table->uuid('sinodal_id');
            $table->boolean('status')->default(true);
            $table->boolean('outro_modelo')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estado_id')->references('id')->on('estados');
            $table->foreign('regiao_id')->references('id')->on('regioes');
            $table->foreign('sinodal_id')->references('id')->on('sinodais');
            $table->foreign('federacao_id')->references('id')->on('federacoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locais');
    }
}
