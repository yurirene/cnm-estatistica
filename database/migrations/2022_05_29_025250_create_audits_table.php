<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditable', function (Blueprint $table) {
            $table->id();
            $table->string('table')->nullable();
            $table->string('table_id')->nullable();
            $table->string('acao')->nullable();
            $table->string('coluna')->nullable();
            $table->text('valor_antigo')->nullable();
            $table->text('valor_novo')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
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
        Schema::drop('auditable');
    }
}
