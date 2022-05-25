<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formularios_local_v1', function (Blueprint $table) {
            $table->id();
            $table->year('ano_referencia');
            $table->json('aci')->nullable();
            $table->json('perfil')->nullable();
            $table->json('deficiencias')->nullable();
            $table->json('estado_civil')->nullable();
            $table->json('escolaridade')->nullable();
            $table->json('programacoes')->nullable();
            $table->uuid('local_id');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('local_id')->references('id')->on('locais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('formularios_local_v1');
    }
}
