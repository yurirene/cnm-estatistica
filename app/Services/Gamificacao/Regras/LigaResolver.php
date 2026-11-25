<?php

namespace App\Services\Gamificacao\Regras;

use App\Services\Gamificacao\Enums\Liga;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;

class LigaResolver
{
    public function resolver(NaturezaInstancia $natureza, int $sociosAtivos): Liga
    {
        $regras = GamificacaoConfiguracaoService::get("ligas.{$natureza->value}", []);

        $simontonMin = (int) ($regras['simonton']['min'] ?? PHP_INT_MAX);
        $blackfordMin = (int) ($regras['blackford']['min'] ?? PHP_INT_MAX);

        if ($sociosAtivos >= $simontonMin) {
            return Liga::Simonton;
        }

        if ($sociosAtivos >= $blackfordMin) {
            return Liga::Blackford;
        }

        return Liga::Conceicao;
    }

    public function faixa(NaturezaInstancia $natureza, Liga $liga): string
    {
        return (string) GamificacaoConfiguracaoService::get("ligas.{$natureza->value}.{$liga->value}.faixa", '');
    }
}
