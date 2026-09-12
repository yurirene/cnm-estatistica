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
        Schema::table('digestos', function (Blueprint $table) {
            $table->fullText(['titulo', 'texto'], 'digestos_titulo_texto_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digestos', function (Blueprint $table) {
            $table->dropFullText('digestos_titulo_texto_fulltext');
        });
    }
};
