<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('resolutions') && !Schema::hasTable('resolucoes')) {
            Schema::rename('resolutions', 'resolucoes');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('resolucoes') && !Schema::hasTable('resolutions')) {
            Schema::rename('resolucoes', 'resolutions');
        }
    }
};
