<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolucoes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('origem', ['congresso', 'executiva', 'diretoria']);
            $table->enum('status', ['pendente', 'em_andamento', 'concluido', 'cancelado'])->default('pendente');
            $table->enum('prioridade', ['baixa', 'media', 'alta'])->default('media');
            $table->date('data_aprovacao');
            $table->date('prazo_final')->nullable();
            $table->foreignUuid('responsavel_id')->constrained('users');
            $table->foreignUuid('criado_por')->constrained('users');
            $table->json('anexos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolucoes');
    }
};
