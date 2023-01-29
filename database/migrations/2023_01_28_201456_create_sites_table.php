<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->uuid('sinodal_id');
            $table->json('configuracoes');
            $table->string('url');
            $table->bigInteger('modelo_id')->unsigned();
            $table->timestamps();

            $table->foreign('sinodal_id')->references('id')->on('sinodais')->onDelete('cascade');
            $table->foreign('modelo_id')->references('id')->on('sites_modelos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
