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
        Schema::create('congresso_reunioes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    // insert uma reuniao
    \DB::table('congresso_reunioes')->insert([
        'nome' => 'Congresso Nacional 2026',
        'status' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('congresso_reunioes');
    }
};
