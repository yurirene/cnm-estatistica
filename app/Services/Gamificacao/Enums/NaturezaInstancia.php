<?php

namespace App\Services\Gamificacao\Enums;

use App\Services\Gamificacao\GamificacaoConfiguracaoService;

enum NaturezaInstancia: string
{
    case Sinodal = 'sinodal';
    case Federacao = 'federacao';

    public function tetoAnual(): int
    {
        return GamificacaoConfiguracaoService::getInt("tetos.{$this->value}");
    }

    public function tetoCiclo(): int
    {
        return GamificacaoConfiguracaoService::getInt("tetos.ciclo_{$this->value}");
    }
}
