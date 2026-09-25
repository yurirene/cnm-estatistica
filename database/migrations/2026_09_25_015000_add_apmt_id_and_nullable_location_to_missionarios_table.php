<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('missionarios', 'apmt_id')) {
            Schema::table('missionarios', function (Blueprint $table) {
                $table->string('apmt_id')->nullable()->unique()->after('jmn_id');
            });
        }

        DB::statement('ALTER TABLE missionarios MODIFY estado_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE missionarios MODIFY regiao_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (Schema::hasColumn('missionarios', 'apmt_id')) {
            Schema::table('missionarios', function (Blueprint $table) {
                $table->dropUnique(['apmt_id']);
                $table->dropColumn('apmt_id');
            });
        }
    }
};
