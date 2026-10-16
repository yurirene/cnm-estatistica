<?php

namespace App\Services\Gamificacao\Regras;

use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;

class DescontoResolver
{
    public function projetar(NaturezaInstancia $natureza, int $pontosCiclo): int
    {
        $faixas = GamificacaoConfiguracaoService::get("descontos.{$natureza->value}", []);

        foreach ([100, 75, 50] as $percentual) {
            $minimo = (int) ($faixas[$percentual] ?? PHP_INT_MAX);
            if ($pontosCiclo >= $minimo) {
                return $percentual;
            }
        }

        return 0;
    }

    public function pontosParaProximo(NaturezaInstancia $natureza, int $pontosCiclo): int
    {
        $faixas = GamificacaoConfiguracaoService::get("descontos.{$natureza->value}", []);
        $atual = $this->projetar($natureza, $pontosCiclo);

        $proximos = match ($atual) {
            0 => 50,
            50 => 75,
            75 => 100,
            default => null,
        };

        if ($proximos === null) {
            return 0;
        }

        $alvo = (int) ($faixas[$proximos] ?? 0);

        return max(0, $alvo - $pontosCiclo);
    }
}
