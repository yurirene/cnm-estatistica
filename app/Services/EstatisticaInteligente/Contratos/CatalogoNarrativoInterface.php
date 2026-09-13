<?php

namespace App\Services\EstatisticaInteligente\Contratos;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

interface CatalogoNarrativoInterface
{
    /**
     * @return string[]
     */
    public function textos(EventoNarrativoEnum $evento): array;

    public function selecionar(EventoNarrativoEnum $evento, string $contextoHash): string;
}
