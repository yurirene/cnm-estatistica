<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongressoDocumentosRecebidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('congresso_documentos_recebidos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reuniao_id')->constrained('congresso_reunioes')->cascadeOnDelete();

            // Campos para identificar a instância do documento
            $table->foreignUuid('sinodal_id')->nullable()->constrained('sinodais')->cascadeOnDelete();
            $table->foreignUuid('federacao_id')->nullable()->constrained('federacoes')->cascadeOnDelete();
            $table->foreignUuid('local_id')->nullable()->constrained('locais')->cascadeOnDelete();

            // Dados do documento
            $table->string('path')->nullable();
            $table->tinyInteger('tipo_documento')->comment("3 - Doc Sinodal, 7 - Cred Sinodal, 4 - Doc Fed, 8 - Cred Fed, 5 - Doc Local, 9 - Cred Local");
            $table->tinyInteger('status')->default(0)->comment("0 - Pendente, 1 - Recebido");
            $table->timestamps();

            // Índices para otimizar consultas
            $table->index(['reuniao_id', 'sinodal_id', 'federacao_id', 'local_id']);
            $table->index('tipo_documento');
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
        Schema::dropIfExists('congresso_documentos_recebidos');
    }
}
