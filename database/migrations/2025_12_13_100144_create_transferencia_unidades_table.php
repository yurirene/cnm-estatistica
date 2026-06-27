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
        Schema::create('transferencia_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('federacao_id')->nullable()->constrained('federacoes')->nullOnDelete();
            $table->foreignUuid('local_id')->nullable()->constrained('locais')->nullOnDelete();
            $table->foreignUuid('sinodal_origem_id')->nullable()->constrained('sinodais')->nullOnDelete();
            $table->foreignUuid('sinodal_destino_id')->nullable()->constrained('sinodais')->nullOnDelete();
            $table->foreignUuid('federacao_origem_id')->nullable()->constrained('federacoes')->nullOnDelete();
            $table->foreignUuid('federacao_destino_id')->nullable()->constrained('federacoes')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferencia_unidades');
    }
};
