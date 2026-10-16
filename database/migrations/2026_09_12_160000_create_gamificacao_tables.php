<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('gamificacao_auditoria');
        Schema::dropIfExists('gamificacao_conquistas');
        Schema::dropIfExists('gamificacao_pilar_resultados');
        Schema::dropIfExists('gamificacao_placares');

        Schema::create('gamificacao_placares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ciclo', 9);
            $table->year('ano_referencia');
            $table->uuid('sinodal_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->string('liga', 10);
            $table->unsignedInteger('socios_ativos')->default(0);
            $table->unsignedSmallInteger('pontos_ano')->default(0);
            $table->unsignedSmallInteger('pontos_ciclo')->default(0);
            $table->unsignedSmallInteger('posicao_liga')->default(0);
            $table->unsignedSmallInteger('total_na_liga')->default(0);
            $table->unsignedTinyInteger('desconto_projetado')->default(0);
            $table->timestamp('fechado_em')->nullable();
            $table->timestamp('calculado_em')->nullable();
            $table->timestamps();

            $table->foreign('sinodal_id')->references('id')->on('sinodais')->cascadeOnDelete();
            $table->foreign('federacao_id')->references('id')->on('federacoes')->cascadeOnDelete();
            $table->unique(['ciclo', 'ano_referencia', 'sinodal_id'], 'gamificacao_placares_sinodal_unq');
            $table->unique(['ciclo', 'ano_referencia', 'federacao_id'], 'gamificacao_placares_federacao_unq');
            $table->index(['liga', 'ano_referencia', 'ciclo'], 'gamificacao_placares_liga_idx');
        });

        Schema::create('gamificacao_pilar_resultados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('placar_id');
            $table->string('pilar', 40);
            $table->unsignedSmallInteger('pontos')->default(0);
            $table->unsignedSmallInteger('pontos_maximo');
            $table->unsignedTinyInteger('progresso')->default(0);
            $table->string('status', 20);
            $table->json('detalhes')->nullable();
            $table->timestamps();

            $table->foreign('placar_id')
                ->references('id')
                ->on('gamificacao_placares')
                ->cascadeOnDelete();
            $table->unique(['placar_id', 'pilar'], 'gamificacao_pilar_unq');
        });

        Schema::create('gamificacao_conquistas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tipo', 40);
            $table->string('ciclo', 9);
            $table->year('ano_referencia');
            $table->uuid('sinodal_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->unsignedSmallInteger('pontos');
            $table->string('referencia')->nullable();
            $table->timestamp('concedido_em')->nullable();
            $table->uuid('concedido_por')->nullable();
            $table->timestamps();

            $table->foreign('sinodal_id')->references('id')->on('sinodais')->cascadeOnDelete();
            $table->foreign('federacao_id')->references('id')->on('federacoes')->cascadeOnDelete();
            $table->foreign('concedido_por')->references('id')->on('users')->nullOnDelete();
            $table->index(['tipo', 'ano_referencia', 'sinodal_id'], 'gamificacao_conq_sinodal_idx');
            $table->index(['tipo', 'ano_referencia', 'federacao_id'], 'gamificacao_conq_federacao_idx');
        });

        Schema::create('gamificacao_auditoria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('placar_id');
            $table->string('pilar', 40)->nullable();
            $table->json('antes')->nullable();
            $table->json('depois')->nullable();
            $table->string('motivo')->nullable();
            $table->string('origem', 30);
            $table->timestamps();

            $table->foreign('placar_id')
                ->references('id')
                ->on('gamificacao_placares')
                ->cascadeOnDelete();
            $table->index(['placar_id', 'created_at'], 'gamificacao_auditoria_placar_idx');
        });

        DB::statement('ALTER TABLE gamificacao_placares ADD CONSTRAINT gamificacao_placares_pontos_ano_max CHECK (pontos_ano <= 200)');
    }

    public function down(): void
    {
        Schema::dropIfExists('gamificacao_auditoria');
        Schema::dropIfExists('gamificacao_conquistas');
        Schema::dropIfExists('gamificacao_pilar_resultados');
        Schema::dropIfExists('gamificacao_placares');
    }
};
