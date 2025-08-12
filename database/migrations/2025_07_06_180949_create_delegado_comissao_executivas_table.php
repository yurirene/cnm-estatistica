<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegadoComissaoExecutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissao_executiva_delegados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reuniao_id')->constrained('comissao_executiva_reunioes')->cascadeOnDelete();
            $table->foreignUuid('sinodal_id')->constrained('sinodais')->cascadeOnDelete();
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comissao_executiva_delegados');
    }
}
