<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosComissaoExecutivaRecebidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissao_executiva_documentos_recebidos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reuniao_id');
            $table->uuid('sinodal_id');
            $table->string('titulo')->nullable();
            $table->tinyInteger('tipo')
                ->default(3)
                ->comment("3 - Documento Proveniente da Sinodal (SIGCE)");
            $table->tinyInteger('status')
                ->default(0)
                ->comment("0 - Pendente, 1 - Visto, 2 - Recebido, 3 - Não Recebido");
            $table->string('path');
            $table->timestamps();

            $table->foreign('reuniao_id')
                ->references('id')
                ->on('comissao_executiva_reunioes')
                ->cascadeOnDelete();
            $table->foreign('sinodal_id')
                ->references('id')
                ->on('sinodais')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comissao_executiva_documentos_recebidos');
    }
}
