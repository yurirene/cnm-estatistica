<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analises_estatisticas', function (Blueprint $table) {
            $table->id();
            $table->string('nivel', 20);
            $table->string('nivel_id', 64)->default('');
            $table->year('ano_referencia');
            $table->string('tipo', 30)->default('diagnostico');
            $table->string('titulo');
            $table->text('resumo');
            $table->json('conteudo');
            $table->json('dados_contexto_json')->nullable();
            $table->string('modelo_ia', 64)->nullable();
            $table->string('catalogo_version', 20)->nullable();
            $table->string('contexto_hash', 64);
            $table->string('status', 20)->default('concluida');
            $table->timestamp('gerado_em')->nullable();
            $table->timestamps();

            $table->unique(['nivel', 'nivel_id', 'ano_referencia', 'tipo'], 'analises_estatisticas_unica');
            $table->index(['nivel', 'nivel_id', 'ano_referencia'], 'analises_estatisticas_consulta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analises_estatisticas');
    }
};
