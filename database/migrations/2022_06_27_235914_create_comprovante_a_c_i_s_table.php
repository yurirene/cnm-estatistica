<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprovanteACISTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprovantes_aci', function (Blueprint $table) {
            $table->id();
            $table->string('path')->nullable();
            $table->year('ano');
            $table->uuid('sinodal_id');
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('comprovantes_aci');
    }
}
