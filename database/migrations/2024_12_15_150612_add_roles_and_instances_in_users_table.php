<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolesAndInstancesInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned();
            $table->uuid('local_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->uuid('sinodal_id')->nullable();
            $table->integer('regiao_id')->nullable();

            $table->foreign('local_id')->references('id')->on('locais')->cascadeOnDelete();
            $table->foreign('federacao_id')->references('id')->on('federacoes')->cascadeOnDelete();
            $table->foreign('sinodal_id')->references('id')->on('sinodais')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
            $table->dropColumn('regiao_id');
            $table->dropConstrainedForeignId('local_id');
            $table->dropConstrainedForeignId('federacao_id');
            $table->dropConstrainedForeignId('sinodal_id');
        });
    }
}
