<?php

namespace App\Services\Gamificacao\Regras;

use App\Exceptions\GamificacaoException;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\Pilar;

class ValoresLegaisPilar
{
    public function validar(Pilar $pilar, int $pontos): int
    {
        $legais = $pilar->valoresLegais();

        if (! in_array($pontos, $legais, true)) {
            throw new GamificacaoException(
                "Pontuação ilegal para o pilar {$pilar->value}: {$pontos}."
            );
        }

        return $pontos;
    }

    public function limitarTeto(NaturezaInstancia $natureza, int $pontosAno): int
    {
        return min($pontosAno, $natureza->tetoAnual());
    }
}
