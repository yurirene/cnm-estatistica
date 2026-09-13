<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('digestos', function (Blueprint $table) {
            $table->string('tipo_documento', 20)->nullable()->after('tipo_reuniao_id')->index();
            $table->string('numero_documento', 50)->nullable()->after('tipo_documento')->index();
            $table->string('comissao')->nullable()->after('numero_documento')->index();
        });

        DB::table('digestos')->where('titulo', 'like', 'Relatório%')->update(['tipo_documento' => 'relatorio_comissao']);
        DB::table('digestos')->where('titulo', 'like', 'Relatorio%')->update(['tipo_documento' => 'relatorio_comissao']);
        DB::table('digestos')->where('titulo', 'like', 'Proposta%')->update(['tipo_documento' => 'proposta']);
        DB::table('digestos')->where('titulo', 'like', 'Consulta%')->update(['tipo_documento' => 'consulta']);
    }

    public function down(): void
    {
        Schema::table('digestos', function (Blueprint $table) {
            $table->dropColumn(['tipo_documento', 'numero_documento', 'comissao']);
        });
    }
};
