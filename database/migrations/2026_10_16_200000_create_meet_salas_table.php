<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meet_salas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('para_user_id')->constrained('users');
            $table->string('titulo', 150);
            $table->dateTime('inicia_em');
            $table->dateTime('termina_em');
            $table->string('meet_name');
            $table->string('meet_uri');
            $table->string('meet_code')->nullable();
            $table->timestamps();

            $table->index('inicia_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meet_salas');
    }
};
