<?php

namespace App\Services\EstatisticaInteligente\DTOs;

use App\Services\EstatisticaInteligente\Enums\SeveridadeInsightEnum;

final class ItemAnaliseDTO
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $descricao,
        public readonly SeveridadeInsightEnum $tipo,
    ) {
    }

    /**
     * @return array{titulo: string, descricao: string, tipo: string}
     */
    public function toArray(): array
    {
        return [
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo->value,
        ];
    }
}
