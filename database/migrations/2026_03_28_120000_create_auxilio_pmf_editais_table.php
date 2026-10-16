<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auxilio_pmf_editais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('titulo');
            $table->year('ano');
            $table->date('data_limite_proposta');
            $table->string('link_edital');
            $table->string('link_proposta');
            $table->string('link_relatorio');
            $table->boolean('recebimento_aberto')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auxilio_pmf_editais');
    }
};
