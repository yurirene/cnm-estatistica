<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $colunasCredenciais = [
                'google_drive_client_id',
                'google_drive_client_secret',
                'google_drive_refresh_token',
                'google_drive_access_token',
                'google_drive_connected_at',
            ];

            foreach ($colunasCredenciais as $coluna) {
                if (Schema::hasColumn('users', $coluna)) {
                    $table->dropColumn($coluna);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_drive_client_id')) {
                $table->text('google_drive_client_id')->nullable()->after('telegram_chat_id');
                $table->text('google_drive_client_secret')->nullable()->after('google_drive_client_id');
                $table->text('google_drive_refresh_token')->nullable()->after('google_drive_client_secret');
                $table->text('google_drive_access_token')->nullable()->after('google_drive_refresh_token');
                $table->timestamp('google_drive_connected_at')->nullable()->after('google_drive_folder');
            }
        });
    }
};
