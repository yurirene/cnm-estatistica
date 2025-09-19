<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongressoDelegadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('congresso_delegados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reuniao_id')->constrained('congresso_reunioes')->cascadeOnDelete();

            // Campos para identificar a instância do delegado
            $table->foreignUuid('sinodal_id')->nullable()->constrained('sinodais')->cascadeOnDelete();
            $table->foreignUuid('federacao_id')->nullable()->constrained('federacoes')->cascadeOnDelete();
            $table->foreignUuid('local_id')->nullable()->constrained('locais')->cascadeOnDelete();

            // Dados do delegado
            $table->string('nome')->nullable();
            $table->string('cpf')->nullable();
            $table->string('telefone')->nullable();
            $table->string('path_credencial')->nullable();
            $table->boolean('credencial')->default(false);
            $table->boolean('suplente')->default(false);
            $table->boolean('pago')->default(false);
            $table->tinyInteger('status')
                ->default(1)
                ->comment("-1 - Rejeitada, 0 - Pendente, 1 - Em análise, 2 - Confirmada");
            $table->timestamps();

            // Índices para otimizar consultas
            $table->index(['reuniao_id', 'sinodal_id', 'federacao_id', 'local_id']);
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
        Schema::dropIfExists('congresso_delegados');
    }
}
