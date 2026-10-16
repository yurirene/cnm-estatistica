<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auxilio_pmf_propostas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('edital_id');
            $table->uuid('sinodal_id');
            $table->string('path_proposta')->nullable();
            $table->string('status')->default('pendente');
            $table->string('path_relatorio')->nullable();
            $table->timestamps();

            $table->unique(['edital_id', 'sinodal_id']);

            $table->foreign('edital_id')
                ->references('id')
                ->on('auxilio_pmf_editais')
                ->cascadeOnDelete();

            $table->foreign('sinodal_id')
                ->references('id')
                ->on('sinodais')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auxilio_pmf_propostas');
    }
};
