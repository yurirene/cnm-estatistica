<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosComisaoExecutivasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comisao_executiva_documentos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tipo_documento_id')->unsigned();
            $table->bigInteger('reuniao_id')->unsigned();
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
        Schema::dropIfExists('comisao_executiva_documentos');
    }
}
