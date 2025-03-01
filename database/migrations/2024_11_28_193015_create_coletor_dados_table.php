<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColetorDadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coletor_dados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('resposta')->nullable();
            $table->boolean('status')->default(false);
            $table->year('ano');
            $table->uuid('local_id');
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
        Schema::dropIfExists('coletor_dados');
    }
}
