<?php

namespace App\Services\EstatisticaInteligente\Calculo;

final class IndicadorDerivador
{
    /**
     * @param  array<string, mixed>  $perfil
     * @param  array<string, mixed>  $programacoes
     * @param  array<string, mixed>|null  $perfilAnterior
     * @param  array<string, mixed>|null  $programacoesAnterior
     * @return array<string, mixed>
     */
    public function derivar(
        array $perfil,
        array $programacoes,
        ?array $perfilAnterior = null,
        ?array $programacoesAnterior = null,
    ): array {
        $ativos = $this->inteiro($perfil['ativos'] ?? 0);
        $cooperadores = $this->inteiro($perfil['cooperadores'] ?? 0);
        $totalSocios = $ativos + $cooperadores;
        $ativosAnterior = $perfilAnterior === null ? null : $this->inteiro($perfilAnterior['ativos'] ?? 0);

        $crescimento = null;
        if ($ativosAnterior !== null && $ativosAnterior > 0) {
            $crescimento = (($ativos - $ativosAnterior) / $ativosAnterior) * 100;
        }

        $faixas = [
            'menores_19' => $this->inteiro($perfil['menor19'] ?? 0),
            '19_23' => $this->inteiro($perfil['de19a23'] ?? 0),
            '24_29' => $this->inteiro($perfil['de24a29'] ?? 0),
            '30_35' => $this->inteiro($perfil['de30a35'] ?? 0),
        ];

        $percentuais = [];
        foreach ($faixas as $codigo => $valor) {
            $percentuais['percentual_'.$codigo] = $totalSocios > 0 ? ($valor * 100) / $totalSocios : 0.0;
        }

        $predominante = array_key_first($faixas);
        $maximo = -1;
        foreach ($faixas as $codigo => $valor) {
            if ($valor > $maximo) {
                $maximo = $valor;
                $predominante = $codigo;
            }
        }

        $renovacao = $totalSocios > 0
            ? (($faixas['menores_19'] + $faixas['19_23']) * 100) / $totalSocios
            : 0.0;

        $atividades = $this->atividades($programacoes);
        $totalAtividades = array_sum($atividades);
        $diversidade = count(array_filter($atividades, static fn (int $valor) => $valor > 0));

        $percentuaisAtividade = [];
        foreach ($atividades as $codigo => $valor) {
            $percentuaisAtividade['percentual_atividade_'.$codigo] = $totalAtividades > 0
                ? ($valor * 100) / $totalAtividades
                : 0.0;
        }

        $totalAnterior = null;
        $variacaoAtividades = null;
        if ($programacoesAnterior !== null) {
            $totalAnterior = array_sum($this->atividades($programacoesAnterior));
            if ($totalAnterior > 0) {
                $variacaoAtividades = (($totalAtividades - $totalAnterior) / $totalAnterior) * 100;
            }
        }

        $concentracao = $percentuaisAtividade === [] ? null : max($percentuaisAtividade);

        return array_merge($percentuais, $percentuaisAtividade, [
            'membros_ativos' => $ativos,
            'membros_ativos_anterior' => $ativosAnterior,
            'crescimento_ativos_anual' => $crescimento,
            'indice_renovacao_geracional' => $renovacao,
            'faixa_etaria_predominante' => $predominante,
            'total_atividades' => $totalAtividades,
            'diversidade_atividades' => $diversidade,
            'concentracao_categoria_percentual' => $concentracao,
            'variacao_atividades_anual' => $variacaoAtividades,
        ]);
    }

    /**
     * @param  array<string, mixed>  $programacoes
     * @return array<string, int>
     */
    public function atividades(array $programacoes): array
    {
        return [
            'espiritual' => $this->inteiro($programacoes['espiritual'] ?? 0),
            'evangelistica' => $this->inteiro($programacoes['evangelistico'] ?? $programacoes['evangelistica'] ?? 0),
            'social' => $this->inteiro($programacoes['social'] ?? 0),
            'recreativa' => $this->inteiro($programacoes['recreativo'] ?? $programacoes['recreativa'] ?? 0),
            'oracao' => $this->inteiro($programacoes['oracao'] ?? 0),
        ];
    }

    private function inteiro(mixed $valor): int
    {
        return is_numeric($valor) ? (int) $valor : 0;
    }
}
