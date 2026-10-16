<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;

trait MontaContexto
{
    /**
     * @param  array<string, mixed>  $over
     */
    protected function contexto(array $over = []): ContextoIaDTO
    {
        $base = [
            'periodo' => 2026,
            'nivel' => 'local',
            'organizacao_id' => 'ump-1',
            'indicadores' => [],
            'historico' => [],
            'comparativo' => null,
            'anomalias' => [],
            'qualidade' => null,
        ];

        $dados = array_replace_recursive($base, $over);

        return ContextoIaDTO::fromArray($dados);
    }

    /**
     * @param  array<int, \App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO>  $eventos
     * @return string[]
     */
    protected function codigos(array $eventos): array
    {
        return array_map(static fn ($evento) => $evento->codigo->value, $eventos);
    }
}
