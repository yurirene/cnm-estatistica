<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('digestos')
            ->where('tipo_documento', 'relatorio')
            ->update(['tipo_documento' => 'relatorio_comissao']);
    }

    public function down(): void
    {
        DB::table('digestos')
            ->where('tipo_documento', 'relatorio_comissao')
            ->update(['tipo_documento' => 'relatorio']);
    }
};
