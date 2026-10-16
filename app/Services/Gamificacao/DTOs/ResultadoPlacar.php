<?php

namespace App\Services\Gamificacao\DTOs;

use App\Services\Gamificacao\Enums\Liga;
use App\Services\Gamificacao\Enums\NaturezaInstancia;

class ResultadoPlacar
{
    /**
     * @param  ResultadoPilar[]  $pilares
     */
    public function __construct(
        public readonly NaturezaInstancia $natureza,
        public readonly Liga $liga,
        public readonly string $faixaLiga,
        public readonly int $sociosAtivos,
        public readonly int $pontosAno,
        public readonly int $tetoAno,
        public readonly int $pontosCiclo,
        public readonly int $tetoCiclo,
        public readonly int $descontoProjetado,
        public readonly int $pontosParaProximoDesconto,
        public readonly array $pilares,
        public readonly int $ano,
        public readonly string $ciclo,
        public readonly ?string $sinodalId,
        public readonly ?string $federacaoId,
    ) {
    }

    /** @return ResultadoPilar[] */
    public function pilares(): array
    {
        return $this->pilares;
    }
}
