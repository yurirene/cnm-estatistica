<?php

namespace App\Services\EstatisticaInteligente\Calculo;

final class PercentilCalculator
{
    /**
     * @param  float[]|int[]  $pares
     */
    public function calcular(float $valor, array $pares): ?float
    {
        if (count($pares) < 2) {
            return null;
        }

        $abaixoOuIgual = 0;
        foreach ($pares as $par) {
            if ((float) $par <= $valor) {
                $abaixoOuIgual++;
            }
        }

        return ($abaixoOuIgual / count($pares)) * 100;
    }
}
