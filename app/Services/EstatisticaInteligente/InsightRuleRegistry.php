<?php

namespace App\Services\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;

final class InsightRuleRegistry
{
    /**
     * @param  InsightRuleInterface[]  $regras
     */
    public function __construct(private readonly array $regras)
    {
    }

    /**
     * @return InsightRuleInterface[]
     */
    public function todas(): array
    {
        return $this->regras;
    }

    /**
     * @return EventoNarrativoDTO[]
     */
    public function avaliar(ContextoIaDTO $contexto): array
    {
        $eventos = [];

        foreach ($this->regras as $regra) {
            foreach ($regra->avaliar($contexto) as $evento) {
                $eventos[] = $evento;
            }
        }

        return $eventos;
    }
}
