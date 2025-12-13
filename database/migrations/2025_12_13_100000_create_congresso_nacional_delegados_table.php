<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongressoNacionalDelegadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('congresso_nacional_delegados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('federacao_id')->nullable()->constrained('federacoes')->cascadeOnDelete();
            $table->foreignUuid('sinodal_id')->nullable()->constrained('sinodais')->cascadeOnDelete();
            $table->string('nome')->nullable();
            $table->string('cpf')->nullable();
            $table->string('telefone')->nullable();
            $table->string('path_credencial')->nullable();
            $table->boolean('credencial')->default(false);
            $table->boolean('pago')->default(false);
            $table->tinyInteger('oficial')->default(0)->comment("0 - Não, 1 - Diácono, 2 - Presbítero");
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
        Schema::dropIfExists('congresso_nacional_delegados');
    }
}

