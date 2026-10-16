<?php

namespace App\Services\EstatisticaInteligente\Regras;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class CrescimentoInsightRule implements InsightRuleInterface
{
    public function __construct(private readonly FaixasNarrativas $faixas)
    {
    }

    public function categoria(): CategoriaIndicadorEnum
    {
        return CategoriaIndicadorEnum::Crescimento;
    }

    public function avaliar(ContextoIaDTO $contexto): array
    {
        $eventos = [];
        $variaveisBase = $this->variaveisBase($contexto);

        $valorAnterior = $this->valorAnterior($contexto);
        if ($valorAnterior === 0.0) {
            return [EventoNarrativoDTO::de(EventoNarrativoEnum::CRESCIMENTO_INDETERMINADO, $variaveisBase)];
        }

        $variacao = $contexto->indicador('crescimento_ativos_anual');
        if (! is_numeric($variacao)) {
            return [];
        }

        $variacao = (float) $variacao;
        $variaveis = array_merge($variaveisBase, [
            'percentual' => abs($variacao),
            'variacao' => $variacao,
        ]);

        $evento = $this->classificar($variacao);
        if ($evento !== null) {
            $eventos[] = EventoNarrativoDTO::de($evento, $variaveis);
        }

        if ($this->houveRecuperacao($contexto, $variacao)) {
            $eventos[] = EventoNarrativoDTO::de(EventoNarrativoEnum::RECUPERACAO_POS_RETRACAO, $variaveis);
        }

        return $eventos;
    }

    private function classificar(float $variacao): ?EventoNarrativoEnum
    {
        if ($variacao >= $this->faixas->crescimentoForte) {
            return EventoNarrativoEnum::CRESCIMENTO_FORTE;
        }

        if ($variacao >= $this->faixas->crescimentoModerado) {
            return EventoNarrativoEnum::CRESCIMENTO_MODERADO;
        }

        if ($variacao > -$this->faixas->estabilidade) {
            return EventoNarrativoEnum::ESTABILIDADE;
        }

        if ($variacao > -$this->faixas->retracaoLeve) {
            return EventoNarrativoEnum::RETRACAO_LEVE;
        }

        return EventoNarrativoEnum::RETRACAO_FORTE;
    }

    private function houveRecuperacao(ContextoIaDTO $contexto, float $variacaoAtual): bool
    {
        $variacaoAnterior = $contexto->historicoIndicador($contexto->periodo - 1, 'crescimento_ativos_anual');
        if (! is_numeric($variacaoAnterior)) {
            return false;
        }

        return (float) $variacaoAnterior <= -$this->faixas->recuperacaoRetracao
            && $variacaoAtual >= $this->faixas->recuperacaoAlta;
    }

    private function valorAnterior(ContextoIaDTO $contexto): ?float
    {
        $direto = $contexto->indicador('membros_ativos_anterior');
        if (is_numeric($direto)) {
            return (float) $direto;
        }

        $historico = $contexto->historicoIndicador($contexto->periodo - 1, 'membros_ativos');
        if (is_numeric($historico)) {
            return (float) $historico;
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function variaveisBase(ContextoIaDTO $contexto): array
    {
        return [
            'indicador_nome' => 'membros ativos',
            'ano_atual' => $contexto->periodo,
            'ano_anterior' => $contexto->periodo > 0 ? $contexto->periodo - 1 : $contexto->periodo,
        ];
    }
}
