<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongressoReunioesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('congresso_reunioes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ano');
            $table->string('local');
            $table->text('descricao')->nullable();
            $table->datetime('data_inicio')->nullable();
            $table->datetime('data_fim')->nullable();
            $table->boolean('aberto')->default(true);
            $table->boolean('diretoria')->default(false);
            $table->boolean('relatorio_estatistico')->default(false);
            $table->tinyInteger('status')->default(1)->comment("0 - Inativo, 1 - Ativo, 2 - Encerrado");

            // Campos para identificar o tipo de congresso
            $table->foreignUuid('sinodal_id')->nullable()->constrained('sinodais')->cascadeOnDelete();
            $table->foreignUuid('federacao_id')->nullable()->constrained('federacoes')->cascadeOnDelete();
            $table->foreignUuid('local_id')->nullable()->constrained('locais')->cascadeOnDelete();

            $table->timestamps();

            // Índices para otimizar consultas
            $table->index(['sinodal_id', 'federacao_id', 'local_id']);
            $table->index('ano');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('congresso_reunioes');
    }
}
