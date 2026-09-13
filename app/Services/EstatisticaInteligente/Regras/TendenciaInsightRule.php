<?php

namespace App\Services\EstatisticaInteligente\Regras;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class TendenciaInsightRule implements InsightRuleInterface
{
    public function __construct(private readonly FaixasNarrativas $faixas)
    {
    }

    public function categoria(): CategoriaIndicadorEnum
    {
        return CategoriaIndicadorEnum::Tendencia;
    }

    public function avaliar(ContextoIaDTO $contexto): array
    {
        $variacoes = $this->variacoesConsecutivas($contexto);
        if (count($variacoes) < $this->faixas->anosTendencia) {
            return [];
        }

        $sinais = array_map(static function (float $variacao): int {
            if ($variacao > 0) {
                return 1;
            }

            if ($variacao < 0) {
                return -1;
            }

            return 0;
        }, $variacoes);

        $unicos = array_values(array_unique($sinais));
        $variaveis = [
            'indicador_nome' => 'membros ativos',
            'ano_atual' => $contexto->periodo,
        ];

        if (count($unicos) === 1) {
            if ($unicos[0] === 1) {
                return [EventoNarrativoDTO::de(EventoNarrativoEnum::TENDENCIA_ALTA_CONSISTENTE, $variaveis)];
            }

            if ($unicos[0] === -1) {
                return [EventoNarrativoDTO::de(EventoNarrativoEnum::TENDENCIA_QUEDA_CONSISTENTE, $variaveis)];
            }

            return [];
        }

        return [EventoNarrativoDTO::de(EventoNarrativoEnum::TENDENCIA_OSCILANTE, $variaveis)];
    }

    /**
     * @return float[]
     */
    private function variacoesConsecutivas(ContextoIaDTO $contexto): array
    {
        $variacoes = [];

        for ($i = 0; $i < $this->faixas->anosTendencia; $i++) {
            $ano = $contexto->periodo - $i;
            $valor = $i === 0
                ? $contexto->indicador('crescimento_ativos_anual')
                : $contexto->historicoIndicador($ano, 'crescimento_ativos_anual');

            if (! is_numeric($valor)) {
                return [];
            }

            $variacoes[] = (float) $valor;
        }

        return $variacoes;
    }
}
