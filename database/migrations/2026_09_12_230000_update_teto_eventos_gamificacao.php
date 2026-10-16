<?php

use App\Models\Parametro;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        Parametro::updateOrCreate(
            ['nome' => 'pilares.eventos.max', 'area' => GamificacaoConfiguracaoService::AREA],
            [
                'descricao' => 'Eventos — teto',
                'valor' => '17',
                'tipo' => 'number',
            ]
        );

        Cache::forget('gamificacao.config.overrides');
    }

    public function down(): void
    {
        Parametro::updateOrCreate(
            ['nome' => 'pilares.eventos.max', 'area' => GamificacaoConfiguracaoService::AREA],
            [
                'descricao' => 'Eventos — teto',
                'valor' => '15',
                'tipo' => 'number',
            ]
        );

        Cache::forget('gamificacao.config.overrides');
    }
};
