<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('role_user');
    }
}
