<?php

namespace App\Services\EstatisticaInteligente\Regras;

final class FaixasNarrativas
{
    public function __construct(
        public readonly float $crescimentoForte = 15.0,
        public readonly float $crescimentoModerado = 5.0,
        public readonly float $estabilidade = 5.0,
        public readonly float $retracaoLeve = 15.0,
        public readonly float $renovacaoAlta = 60.0,
        public readonly float $renovacaoBaixa = 40.0,
        public readonly int $diversidadeAlta = 5,
        public readonly int $diversidadeBaixa = 2,
        public readonly float $concentracaoCategoria = 60.0,
        public readonly float $percentilAcima = 75.0,
        public readonly float $percentilAbaixo = 25.0,
        public readonly float $qualidadeAlta = 85.0,
        public readonly float $qualidadeMedia = 60.0,
        public readonly float $faixaEtariaConcentracao = 40.0,
        public readonly float $variacaoAtividades = 5.0,
        public readonly int $anosTendencia = 3,
        public readonly float $recuperacaoRetracao = 5.0,
        public readonly float $recuperacaoAlta = 5.0,
    ) {
    }

    public static function fromConfig(): self
    {
        $faixas = config('estatistica_regras', []);

        return new self(
            crescimentoForte: (float) ($faixas['crescimento_forte'] ?? 15.0),
            crescimentoModerado: (float) ($faixas['crescimento_moderado'] ?? 5.0),
            estabilidade: (float) ($faixas['estabilidade'] ?? 5.0),
            retracaoLeve: (float) ($faixas['retracao_leve'] ?? 15.0),
            renovacaoAlta: (float) ($faixas['renovacao_alta'] ?? 60.0),
            renovacaoBaixa: (float) ($faixas['renovacao_baixa'] ?? 40.0),
            diversidadeAlta: (int) ($faixas['diversidade_alta'] ?? 5),
            diversidadeBaixa: (int) ($faixas['diversidade_baixa'] ?? 2),
            concentracaoCategoria: (float) ($faixas['concentracao_categoria'] ?? 60.0),
            percentilAcima: (float) ($faixas['percentil_acima'] ?? 75.0),
            percentilAbaixo: (float) ($faixas['percentil_abaixo'] ?? 25.0),
            qualidadeAlta: (float) ($faixas['qualidade_alta'] ?? 85.0),
            qualidadeMedia: (float) ($faixas['qualidade_media'] ?? 60.0),
            faixaEtariaConcentracao: (float) ($faixas['faixa_etaria_concentracao'] ?? 40.0),
            variacaoAtividades: (float) ($faixas['variacao_atividades'] ?? 5.0),
            anosTendencia: (int) ($faixas['anos_tendencia'] ?? 3),
            recuperacaoRetracao: (float) ($faixas['recuperacao_retracao'] ?? 5.0),
            recuperacaoAlta: (float) ($faixas['recuperacao_alta'] ?? 5.0),
        );
    }
}
