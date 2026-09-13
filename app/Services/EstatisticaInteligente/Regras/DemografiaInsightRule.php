<?php

namespace App\Services\EstatisticaInteligente\Regras;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class DemografiaInsightRule implements InsightRuleInterface
{
    private const FAIXAS_JOVENS = ['menores_19', '19_23'];

    private const FAIXAS_ADULTAS = ['24_29', '30_35'];

    private const PERCENTUAIS = [
        'menores_19' => 'percentual_menores_19',
        '19_23' => 'percentual_19_23',
        '24_29' => 'percentual_24_29',
        '30_35' => 'percentual_30_35',
    ];

    public function __construct(private readonly FaixasNarrativas $faixas)
    {
    }

    public function categoria(): CategoriaIndicadorEnum
    {
        return CategoriaIndicadorEnum::Demografia;
    }

    public function avaliar(ContextoIaDTO $contexto): array
    {
        $eventos = [];
        $variaveis = [
            'ano_atual' => $contexto->periodo,
            'ano_anterior' => $contexto->periodo > 0 ? $contexto->periodo - 1 : $contexto->periodo,
        ];

        $renovacao = $this->eventoRenovacao($contexto, $variaveis);
        if ($renovacao !== null) {
            $eventos[] = $renovacao;
        }

        $faixa = $this->eventoFaixaEtaria($contexto, $variaveis);
        if ($faixa !== null) {
            $eventos[] = $faixa;
        }

        return $eventos;
    }

    /**
     * @param  array<string, mixed>  $variaveis
     */
    private function eventoRenovacao(ContextoIaDTO $contexto, array $variaveis): ?EventoNarrativoDTO
    {
        $indice = $contexto->indicador('indice_renovacao_geracional');
        if (! is_numeric($indice)) {
            return null;
        }

        $indice = (float) $indice;
        $variaveis['indice'] = $indice;

        if ($indice >= $this->faixas->renovacaoAlta) {
            return EventoNarrativoDTO::de(EventoNarrativoEnum::RENOVACAO_ALTA, $variaveis);
        }

        if ($indice < $this->faixas->renovacaoBaixa) {
            return EventoNarrativoDTO::de(EventoNarrativoEnum::RENOVACAO_BAIXA, $variaveis);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $variaveis
     */
    private function eventoFaixaEtaria(ContextoIaDTO $contexto, array $variaveis): ?EventoNarrativoDTO
    {
        $percentuais = [];
        foreach (self::PERCENTUAIS as $codigo => $indicador) {
            $valor = $contexto->indicador($indicador);
            if (is_numeric($valor)) {
                $percentuais[$codigo] = (float) $valor;
            }
        }

        if ($percentuais === []) {
            return null;
        }

        $maximo = max($percentuais);
        if ($maximo < $this->faixas->faixaEtariaConcentracao) {
            return EventoNarrativoDTO::de(EventoNarrativoEnum::DISTRIBUICAO_EQUILIBRADA, $variaveis);
        }

        $predominante = $contexto->indicador('faixa_etaria_predominante');
        if (! is_string($predominante) || $predominante === '') {
            $predominante = (string) array_search($maximo, $percentuais, true);
        }

        $percentualPredominante = $percentuais[$predominante] ?? $maximo;
        if ($percentualPredominante < $this->faixas->faixaEtariaConcentracao) {
            return EventoNarrativoDTO::de(EventoNarrativoEnum::DISTRIBUICAO_EQUILIBRADA, $variaveis);
        }

        $variaveis['percentual'] = $percentualPredominante;
        $variaveis['faixa'] = $predominante;

        if (in_array($predominante, self::FAIXAS_JOVENS, true)) {
            return EventoNarrativoDTO::de(EventoNarrativoEnum::CONCENTRACAO_FAIXA_JOVEM, $variaveis);
        }

        if (in_array($predominante, self::FAIXAS_ADULTAS, true)) {
            return EventoNarrativoDTO::de(EventoNarrativoEnum::CONCENTRACAO_FAIXA_ADULTA, $variaveis);
        }

        return null;
    }
}
