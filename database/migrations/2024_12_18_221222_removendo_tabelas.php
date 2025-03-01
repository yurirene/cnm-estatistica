<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovendoTabelas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('usuario_regiao');
        Schema::dropIfExists('perfil_usuario');
        Schema::dropIfExists('perfis');
        Schema::dropIfExists('atividades');
        Schema::dropIfExists('demanda_itens');
        Schema::dropIfExists('demandas');
        Schema::dropIfExists('registro_logins');
        Schema::dropIfExists('auditable');
        Schema::dropIfExists('log_erros');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
