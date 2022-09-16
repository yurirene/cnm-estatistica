<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandaItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demanda_itens', function (Blueprint $table) {
            $table->id();
            $table->string('origem')->nullable();
            $table->string('documento')->nullable();
            $table->tinyInteger('nivel')->nullable();
            $table->text('demanda')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('demanda_id')->unsigned();
            $table->uuid('user_id')->nullable();
            $table->timestamps();

            $table->foreign('demanda_id')->references('id')->on('demandas');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demanda_itens');
    }
}
