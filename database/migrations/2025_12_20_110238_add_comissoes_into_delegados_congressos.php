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
            $table->json('comissoes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('congresso_nacional_delegados', function (Blueprint $table) {
            $table->dropColumn('comissoes');
        });
    }
};
