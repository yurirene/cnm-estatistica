<?php

namespace App\Services\EstatisticaInteligente\Contratos;

use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;

interface InsightRuleInterface
{
    /**
     * @return EventoNarrativoDTO[]
     */
    public function avaliar(ContextoIaDTO $contexto): array;

    public function categoria(): CategoriaIndicadorEnum;
}
