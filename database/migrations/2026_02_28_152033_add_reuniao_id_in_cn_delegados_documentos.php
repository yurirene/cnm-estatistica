<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('congresso_nacional_delegados', function (Blueprint $table) {
            $table->bigInteger('reuniao_id')->nullable();
        });

        Schema::table('congresso_nacional_documentos_recebidos', function (Blueprint $table) {
            $table->bigInteger('reuniao_id')->nullable();
        });

        \DB::table('congresso_nacional_delegados')->update(['reuniao_id' => 1]);
        \DB::table('congresso_nacional_documentos_recebidos')->update(['reuniao_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('congresso_nacional_delegados', function (Blueprint $table) {
            //
            $table->dropColumn('reuniao_id');
        });
        Schema::table('congressso_nacional_documentos_recebidos', function (Blueprint $table) {
            $table->dropColumn('reuniao_id');
        });
    }
};
