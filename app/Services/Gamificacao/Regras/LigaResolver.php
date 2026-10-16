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

        $ouroMin = (int) ($regras['ouro']['min'] ?? PHP_INT_MAX);
        $prataMin = (int) ($regras['prata']['min'] ?? PHP_INT_MAX);

        if ($sociosAtivos >= $ouroMin) {
            return Liga::Ouro;
        }

        if ($sociosAtivos >= $prataMin) {
            return Liga::Prata;
        }

        return Liga::Bronze;
    }

    public function faixa(NaturezaInstancia $natureza, Liga $liga): string
    {
        return (string) GamificacaoConfiguracaoService::get("ligas.{$natureza->value}.{$liga->value}.faixa", '');
    }
}
