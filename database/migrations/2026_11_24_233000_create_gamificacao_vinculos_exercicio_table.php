<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamificacao_vinculos_exercicio', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->year('ano_referencia');
            $table->uuid('local_id');
            $table->uuid('federacao_id');
            $table->uuid('sinodal_id');
            $table->timestamps();

            $table->foreign('local_id')->references('id')->on('locais')->cascadeOnDelete();
            $table->foreign('federacao_id')->references('id')->on('federacoes')->cascadeOnDelete();
            $table->foreign('sinodal_id')->references('id')->on('sinodais')->cascadeOnDelete();
            $table->unique(['ano_referencia', 'local_id'], 'gamificacao_vinculos_ano_local_unq');
            $table->index(['ano_referencia', 'sinodal_id'], 'gamificacao_vinculos_ano_sinodal_idx');
            $table->index(['ano_referencia', 'federacao_id'], 'gamificacao_vinculos_ano_federacao_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamificacao_vinculos_exercicio');
    }
};
