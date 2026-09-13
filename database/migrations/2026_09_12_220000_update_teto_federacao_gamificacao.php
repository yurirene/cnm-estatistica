<?php

use App\Models\Parametro;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        $this->atualizar('tetos.federacao', '107', 'Teto anual — Federação');
        $this->atualizar('tetos.ciclo_federacao', '428', 'Teto do ciclo — Federação');

        Cache::forget('gamificacao.config.overrides');
    }

    public function down(): void
    {
        $this->atualizar('tetos.federacao', '105', 'Teto anual — Federação');
        $this->atualizar('tetos.ciclo_federacao', '426', 'Teto do ciclo — Federação');

        Cache::forget('gamificacao.config.overrides');
    }

    private function atualizar(string $nome, string $valor, string $descricao): void
    {
        Parametro::updateOrCreate(
            ['nome' => $nome, 'area' => GamificacaoConfiguracaoService::AREA],
            [
                'descricao' => $descricao,
                'valor' => $valor,
                'tipo' => 'number',
            ]
        );
    }
};
