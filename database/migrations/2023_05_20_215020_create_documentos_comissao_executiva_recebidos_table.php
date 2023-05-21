<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosComissaoExecutivaRecebidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissao_executiva_documentos_recebidos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('documento_id')->unsigned();
            $table->bigInteger('reuniao_id')->unsigned();
            $table->bigInteger('sinodal_id')->unsigned();
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
        Schema::dropIfExists('comissao_executiva_documentos_recebidos');
    }
}
