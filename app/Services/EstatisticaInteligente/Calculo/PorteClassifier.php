<?php

namespace App\Services\EstatisticaInteligente\Calculo;

final class PorteClassifier
{
    /**
     * @param  array<int, array{codigo: string, min: int, max: int|null}>  $faixas
     */
    public function __construct(private readonly array $faixas)
    {
    }

    public static function fromConfig(): self
    {
        return new self(config('estatistica_regras.portes', []));
    }

    public function classificar(int $membrosAtivos): ?string
    {
        foreach ($this->faixas as $faixa) {
            $min = (int) ($faixa['min'] ?? 0);
            $max = $faixa['max'] ?? null;

            if ($membrosAtivos < $min) {
                continue;
            }

            if ($max === null || $membrosAtivos <= (int) $max) {
                return (string) $faixa['codigo'];
            }
        }

        return null;
    }

    /**
     * @return array{min: int, max: int|null}|null
     */
    public function intervalo(string $codigo): ?array
    {
        foreach ($this->faixas as $faixa) {
            if ((string) $faixa['codigo'] === $codigo) {
                return [
                    'min' => (int) ($faixa['min'] ?? 0),
                    'max' => $faixa['max'] ?? null,
                ];
            }
        }

        return null;
    }
}
