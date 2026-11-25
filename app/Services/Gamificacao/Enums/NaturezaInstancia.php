<?php

namespace App\Services\Gamificacao\Enums;

use App\Services\Gamificacao\GamificacaoConfiguracaoService;

enum NaturezaInstancia: string
{
    case Sinodal = 'sinodal';
    case Federacao = 'federacao';

    public function tetoAnual(?int $ano = null): int
    {
        $inicio = GamificacaoConfiguracaoService::getInt('ciclo_inicio', 2026);
        if ($ano !== null && $ano === $inicio) {
            return GamificacaoConfiguracaoService::getInt('tetos.ano_inicial', 50);
        }

        return GamificacaoConfiguracaoService::getInt("tetos.{$this->value}");
    }

    public function tetoCiclo(): int
    {
        return GamificacaoConfiguracaoService::getInt("tetos.ciclo_{$this->value}");
    }
}
