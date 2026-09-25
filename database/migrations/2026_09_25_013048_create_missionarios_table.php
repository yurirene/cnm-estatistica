<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missionarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('jmn_id')->nullable()->unique();
            $table->string('apmt_id')->nullable()->unique();
            $table->string('nome');
            $table->string('cidade');
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->unsignedBigInteger('regiao_id')->nullable();
            $table->string('pais');
            $table->string('foto')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('outras_informacoes')->nullable();
            $table->uuid('sinodal_id')->nullable();
            $table->uuid('federacao_id')->nullable();
            $table->timestamp('adotado_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estado_id')->references('id')->on('estados');
            $table->foreign('regiao_id')->references('id')->on('regioes');
            $table->foreign('sinodal_id')->references('id')->on('sinodais');
            $table->foreign('federacao_id')->references('id')->on('federacoes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missionarios');
    }
};
