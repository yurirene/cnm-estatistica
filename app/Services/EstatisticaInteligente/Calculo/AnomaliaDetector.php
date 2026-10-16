<?php

namespace App\Services\EstatisticaInteligente\Calculo;

final class AnomaliaDetector
{
    public function __construct(
        private readonly float $variacaoAnual,
        private readonly float $variacaoExtrema,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            (float) config('estatistica_regras.anomalia_variacao', 30),
            (float) config('estatistica_regras.anomalia_extrema', 50),
        );
    }

    /**
     * @return array<int, array{tipo: string, variacao: float}>
     */
    public function detectar(?float $crescimento): array
    {
        if ($crescimento === null) {
            return [];
        }

        $abs = abs($crescimento);
        if ($abs >= $this->variacaoExtrema) {
            return [['tipo' => 'variacao_extrema', 'variacao' => $crescimento]];
        }

        if ($abs >= $this->variacaoAnual) {
            return [['tipo' => 'variacao_anual', 'variacao' => $crescimento]];
        }

        return [];
    }
}
