<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resolucoes', function (Blueprint $table) {
            $table->boolean('nao_notificar')->default(false)->after('anexos');
        });
    }

    public function down(): void
    {
        Schema::table('resolucoes', function (Blueprint $table) {
            $table->dropColumn('nao_notificar');
        });
    }
};
