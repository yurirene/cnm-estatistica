<?php

namespace App\Services\EstatisticaInteligente\Regras;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class ComparativoInsightRule implements InsightRuleInterface
{
    public function __construct(private readonly FaixasNarrativas $faixas)
    {
    }

    public function categoria(): CategoriaIndicadorEnum
    {
        return CategoriaIndicadorEnum::Comparativo;
    }

    public function avaliar(ContextoIaDTO $contexto): array
    {
        $percentil = $contexto->percentil();
        if ($percentil === null) {
            return [];
        }

        $variaveis = [
            'percentil' => $percentil,
            'ano_atual' => $contexto->periodo,
            'ano_anterior' => $contexto->periodo > 0 ? $contexto->periodo - 1 : $contexto->periodo,
            'grupo_pares' => (string) ($contexto->comparativo['grupo_pares'] ?? 'das organizações de referência'),
        ];

        $eventoAtual = $this->classificar($percentil);
        $eventos = [EventoNarrativoDTO::de($eventoAtual, $variaveis)];

        $percentilAnterior = $contexto->percentilAnterior();
        if ($percentilAnterior !== null) {
            $eventoAnterior = $this->classificar($percentilAnterior);
            if ($this->houveSaltoDeFaixa($eventoAnterior, $eventoAtual)) {
                $eventos[] = EventoNarrativoDTO::de(EventoNarrativoEnum::MUDANCA_RELEVANTE_POSICAO, $variaveis);
            }
        }

        return $eventos;
    }

    private function classificar(float $percentil): EventoNarrativoEnum
    {
        if ($percentil >= $this->faixas->percentilAcima) {
            return EventoNarrativoEnum::ACIMA_MEDIANA_PORTE;
        }

        if ($percentil >= $this->faixas->percentilAbaixo) {
            return EventoNarrativoEnum::NA_MEDIANA_PORTE;
        }

        return EventoNarrativoEnum::ABAIXO_MEDIANA_PORTE;
    }

    private function houveSaltoDeFaixa(EventoNarrativoEnum $anterior, EventoNarrativoEnum $atual): bool
    {
        $extremos = [
            EventoNarrativoEnum::ABAIXO_MEDIANA_PORTE,
            EventoNarrativoEnum::ACIMA_MEDIANA_PORTE,
        ];

        return $anterior !== $atual
            && in_array($anterior, $extremos, true)
            && in_array($atual, $extremos, true);
    }
}
