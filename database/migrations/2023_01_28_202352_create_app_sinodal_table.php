<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppSinodalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_sinodal', function (Blueprint $table) {
            $table->id();
            $table->uuid('sinodal_id');
            $table->bigInteger('app_id')->unsigned();
            $table->timestamps();
            $table->foreign('sinodal_id')->references('id')->on('sinodais')->onDelete('cascade');
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_sinodal');
    }
}
