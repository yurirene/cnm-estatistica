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
        Schema::create('congresso_nacional_documentos_instancias', function (Blueprint $table) {
            $table->id();
            $table->uuid('federacao_id')->nullable();
            $table->uuid('sinodal_id')->nullable();
            $table->boolean('diretoria')->default(false);
            $table->boolean('estatistico')->default(false);
            $table->boolean('planejamento')->default(false);
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('reuniao_id')->nullable();
            $table->timestamps();

            $table->unique(['reuniao_id', 'sinodal_id', 'federacao_id'], 'cn_doc_instancias_reuniao_sinodal_fed_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('congresso_nacional_documentos_instancias');
    }
};
