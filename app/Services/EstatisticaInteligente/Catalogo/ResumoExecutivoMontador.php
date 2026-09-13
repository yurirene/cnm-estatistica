<?php

namespace App\Services\EstatisticaInteligente\Catalogo;

use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\DTOs\ItemAnaliseDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;

final class ResumoExecutivoMontador
{
    /**
     * @param  EventoNarrativoDTO[]  $eventos
     * @param  array<string, ItemAnaliseDTO>  $itensPorEvento
     */
    public function montar(array $eventos, array $itensPorEvento): string
    {
        $frases = [];

        foreach ($this->prioridade() as $categoria) {
            $evento = $this->primeiroDaCategoria($eventos, $categoria);
            if ($evento === null) {
                continue;
            }

            $item = $itensPorEvento[$evento->codigo->value] ?? null;
            if ($item === null) {
                continue;
            }

            $frases[] = rtrim($item->descricao, '.');

            if (count($frases) >= 3) {
                break;
            }
        }

        if ($frases === []) {
            return 'Os dados disponíveis ainda não permitem um panorama narrativo neste ciclo.';
        }

        return implode('. ', $frases).'.';
    }

    /**
     * @return CategoriaIndicadorEnum[]
     */
    private function prioridade(): array
    {
        return [
            CategoriaIndicadorEnum::Crescimento,
            CategoriaIndicadorEnum::Comparativo,
            CategoriaIndicadorEnum::Qualidade,
            CategoriaIndicadorEnum::Anomalia,
        ];
    }

    /**
     * @param  EventoNarrativoDTO[]  $eventos
     */
    private function primeiroDaCategoria(array $eventos, CategoriaIndicadorEnum $categoria): ?EventoNarrativoDTO
    {
        foreach ($eventos as $evento) {
            if ($evento->codigo->categoria() === $categoria) {
                return $evento;
            }
        }

        return null;
    }
}
