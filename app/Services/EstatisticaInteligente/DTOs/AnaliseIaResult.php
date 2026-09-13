<?php

namespace App\Services\EstatisticaInteligente\DTOs;

final class AnaliseIaResult
{
    /**
     * @param  ItemAnaliseDTO[]  $destaques
     * @param  ItemAnaliseDTO[]  $pontosAtencao
     * @param  ItemAnaliseDTO[]  $tendencias
     * @param  ItemAnaliseDTO[]  $comparacoes
     * @param  string[]  $perguntasEstrategicas
     */
    public function __construct(
        public readonly string $titulo,
        public readonly string $resumo,
        public readonly array $destaques,
        public readonly array $pontosAtencao,
        public readonly array $tendencias,
        public readonly array $comparacoes,
        public readonly array $perguntasEstrategicas,
        public readonly string $modeloIa,
    ) {
    }

    /**
     * @return array{
     *     titulo: string,
     *     resumo: string,
     *     destaques: array<int, array{titulo: string, descricao: string, tipo: string}>,
     *     pontos_atencao: array<int, array{titulo: string, descricao: string, tipo: string}>,
     *     tendencias: array<int, array{titulo: string, descricao: string, tipo: string}>,
     *     comparacoes: array<int, array{titulo: string, descricao: string, tipo: string}>,
     *     perguntas_estrategicas: string[]
     * }
     */
    public function toArray(): array
    {
        return [
            'titulo' => $this->titulo,
            'resumo' => $this->resumo,
            'destaques' => array_map(
                static fn (ItemAnaliseDTO $item): array => $item->toArray(),
                $this->destaques
            ),
            'pontos_atencao' => array_map(
                static fn (ItemAnaliseDTO $item): array => $item->toArray(),
                $this->pontosAtencao
            ),
            'tendencias' => array_map(
                static fn (ItemAnaliseDTO $item): array => $item->toArray(),
                $this->tendencias
            ),
            'comparacoes' => array_map(
                static fn (ItemAnaliseDTO $item): array => $item->toArray(),
                $this->comparacoes
            ),
            'perguntas_estrategicas' => $this->perguntasEstrategicas,
        ];
    }
}
