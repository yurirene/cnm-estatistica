<?php

namespace App\Services\EstatisticaInteligente\DTOs;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Enums\SeveridadeInsightEnum;

final class EventoNarrativoDTO
{
    /**
     * @param  array<string, mixed>  $variaveis
     */
    public function __construct(
        public readonly EventoNarrativoEnum $codigo,
        public readonly SeveridadeInsightEnum $severidade,
        public readonly array $variaveis,
    ) {
    }

    /**
     * @param  array<string, mixed>  $variaveis
     */
    public static function de(EventoNarrativoEnum $codigo, array $variaveis = []): self
    {
        return new self($codigo, $codigo->severidade(), $variaveis);
    }
}
