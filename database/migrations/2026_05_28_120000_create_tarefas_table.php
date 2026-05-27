<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('prazo_final')->nullable();
            $table->enum('periodo_notificacao', [
                'diario',
                'a_cada_2_dias',
                'a_cada_3_dias',
                'semanal',
                'quinzenal',
                'mensal',
            ])->default('semanal');
            $table->enum('status', ['pendente', 'concluido'])->default('pendente');
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('ultimo_alerta_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
