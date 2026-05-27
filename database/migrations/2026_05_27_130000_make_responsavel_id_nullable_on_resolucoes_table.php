<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resolucoes', function (Blueprint $table) {
            $table->dropForeign(['responsavel_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE resolucoes MODIFY responsavel_id CHAR(36) NULL');
        } else {
            Schema::table('resolucoes', function (Blueprint $table) {
                $table->uuid('responsavel_id')->nullable()->change();
            });
        }

        Schema::table('resolucoes', function (Blueprint $table) {
            $table->foreign('responsavel_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('resolucoes', function (Blueprint $table) {
            $table->dropForeign(['responsavel_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE resolucoes MODIFY responsavel_id CHAR(36) NOT NULL');
        } else {
            Schema::table('resolucoes', function (Blueprint $table) {
                $table->uuid('responsavel_id')->nullable(false)->change();
            });
        }

        Schema::table('resolucoes', function (Blueprint $table) {
            $table->foreign('responsavel_id')->references('id')->on('users');
        });
    }
};
