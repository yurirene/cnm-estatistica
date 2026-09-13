<?php

namespace App\Services\EstatisticaInteligente\Regras;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class AnomaliaInsightRule implements InsightRuleInterface
{
    public function categoria(): CategoriaIndicadorEnum
    {
        return CategoriaIndicadorEnum::Anomalia;
    }

    public function avaliar(ContextoIaDTO $contexto): array
    {
        if ($contexto->anomalias === []) {
            return [];
        }

        return [
            EventoNarrativoDTO::de(EventoNarrativoEnum::ANOMALIA_DETECTADA, [
                'quantidade' => count($contexto->anomalias),
                'ano_atual' => $contexto->periodo,
            ]),
        ];
    }
}
