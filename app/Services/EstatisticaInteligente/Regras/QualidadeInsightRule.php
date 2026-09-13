<?php

namespace App\Services\EstatisticaInteligente\Regras;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class QualidadeInsightRule implements InsightRuleInterface
{
    public function __construct(private readonly FaixasNarrativas $faixas)
    {
    }

    public function categoria(): CategoriaIndicadorEnum
    {
        return CategoriaIndicadorEnum::Qualidade;
    }

    public function avaliar(ContextoIaDTO $contexto): array
    {
        $score = $contexto->qualidadeScore();
        if ($score === null) {
            return [];
        }

        $variaveis = [
            'score' => $score,
            'ano_atual' => $contexto->periodo,
        ];

        if ($score >= $this->faixas->qualidadeAlta) {
            return [EventoNarrativoDTO::de(EventoNarrativoEnum::QUALIDADE_ALTA, $variaveis)];
        }

        if ($score >= $this->faixas->qualidadeMedia) {
            return [EventoNarrativoDTO::de(EventoNarrativoEnum::QUALIDADE_MEDIA, $variaveis)];
        }

        return [EventoNarrativoDTO::de(EventoNarrativoEnum::QUALIDADE_BAIXA, $variaveis)];
    }
}
