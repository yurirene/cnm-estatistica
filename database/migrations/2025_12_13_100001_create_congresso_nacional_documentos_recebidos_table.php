<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongressoNacionalDocumentosRecebidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('congresso_nacional_documentos_recebidos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sinodal_id')->nullable();
            $table->string('titulo')->nullable();
            $table->tinyInteger('status')
                ->default(0)
                ->comment("0 - Pendente, 1 - Visto, 2 - Recebido, 3 - Não Recebido");
            $table->string('path');
            $table->timestamps();
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
        Schema::dropIfExists('congresso_nacional_documentos_recebidos');
    }
}

