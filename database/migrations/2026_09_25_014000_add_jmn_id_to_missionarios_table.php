<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('missionarios', 'jmn_id')) {
            return;
        }

        Schema::table('missionarios', function (Blueprint $table) {
            $table->unsignedBigInteger('jmn_id')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('missionarios', 'jmn_id')) {
            return;
        }

        Schema::table('missionarios', function (Blueprint $table) {
            $table->dropUnique(['jmn_id']);
            $table->dropColumn('jmn_id');
        });
    }
};
