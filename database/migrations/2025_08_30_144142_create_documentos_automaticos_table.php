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
        Schema::create('comissao_executiva_documentos_automaticos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('relatorio_estatistico')->nullable();
            $table->json('diretoria')->nullable();
            $table->uuid('reuniao_id')->nullable();
            $table->uuid('sinodal_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->uuid('local_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comissao_executiva_documentos_automaticos');
    }
};
