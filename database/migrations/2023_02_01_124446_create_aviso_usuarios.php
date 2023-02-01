<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvisoUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aviso_usuarios', function (Blueprint $table) {
            $table->id();
            $table->uuid('sinodal_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->uuid('local_id')->nullable();
            $table->uuid('user_id');
            $table->boolean('visualizado')->default(false);
            $table->bigInteger('aviso_id')->unsigned();
            $table->timestamps();

            $table->foreign('aviso_id')->references('id')->on('avisos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aviso_usuarios');
    }
}
