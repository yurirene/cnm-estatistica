<?php

namespace App\Services\Formularios;

use Exception;

class GraficoFormularioService
{

    public static function formatarResumo($dados)
    {
        $retorno = [
            'ano_referencia' => $dados->ano_referencia,
            'ativos' => $dados->perfil['ativos'] ?? 0,
            'cooperadores' => $dados->perfil['cooperadores'] ?? 0,
            'homens' => $dados->perfil['homens'] ?? 0,
            'mulheres' => $dados->perfil['mulheres'] ?? 0,
            'menor19' => $dados->perfil['menor19'] ?? 0,
            'de19a23' => $dados->perfil['de19a23'] ?? 0,
            'de24a29' => $dados->perfil['de24a29'] ?? 0,
            'de30a35' => $dados->perfil['de30a35'] ?? 0,
            'fundamental' => $dados->escolaridade['fundamental'] ?? 0,
            'medio' => $dados->escolaridade['medio'] ?? 0,
            'tecnico' => $dados->escolaridade['tecnico'] ?? 0,
            'superior' => $dados->escolaridade['superior'] ?? 0,
            'pos' => $dados->escolaridade['pos'] ?? 0,
            'solteiros' => $dados->estado_civil['solteiros'] ?? 0,
            'casados' => $dados->estado_civil['casados'] ?? 0,
            'divorciados' => $dados->estado_civil['divorciados'] ?? 0,
            'viuvos' => $dados->estado_civil['viuvos'] ?? 0,
            'filhos' => $dados->estado_civil['filhos'] ?? 0,
            'surdos' => $dados->deficiencias['surdos'] ?? 0,
            'auditiva' => $dados->deficiencias['auditiva'] ?? 0,
            'cegos' => $dados->deficiencias['cegos'] ?? 0,
            'baixa_visao' => $dados->deficiencias['baixa_visao'] ?? 0,
            'fisica_inferior' => $dados->deficiencias['fisica_inferior'] ?? 0,
            'fisica_superior' => $dados->deficiencias['fisica_superior'] ?? 0,
            'neurologico' => $dados->deficiencias['neurologico'] ?? 0,
            'intelectual' => $dados->deficiencias['intelectual'] ?? 0,
            'trilha_cnm' => $dados->discipulado['trilha_cnm'] ?? 0,
            'discipulando_cnm' => $dados->discipulado['discipulando_cnm'] ?? 0,
            'discipulando_outro' => $dados->discipulado['discipulando_outro'] ?? 0,
            'sendo_discipulados' => $dados->discipulado['sendo_discipulados'] ?? 0,
            'treinamentos_promovidos' => $dados->organizacao['treinamentos_promovidos'] ?? 0,
            'treinamentos_participados_federacao' => $dados->organizacao['treinamentos_participados_federacao'] ?? 0,
            'treinamentos_participados_sinodal' => $dados->organizacao['treinamentos_participados_sinodal'] ?? 0,
            'treinamentos_participados_cnm' => $dados->organizacao['treinamentos_participados_cnm'] ?? 0,
            'social' => $dados->programacoes['social'] ?? 0,
            'evangelistico' => $dados->programacoes['evangelistico'] ?? 0,
            'espiritual' => $dados->programacoes['espiritual'] ?? 0,
            'recreativo' => $dados->programacoes['recreativo'] ?? 0,
            'oracao' => $dados->programacoes['oracao'] ?? 0,
            'aci' => self::extrairValorAci($dados),
            'aci_repasse' => data_get($dados, 'aci.repasse', 'N'),
            'tem_estrutura' => !empty($dados->estrutura) && is_array($dados->estrutura),
            'ump_organizada' => data_get($dados, 'estrutura.ump_organizada', 0),
            'ump_nao_organizada' => data_get($dados, 'estrutura.ump_nao_organizada', 0),
            'federacao_organizada' => data_get($dados, 'estrutura.federacao_organizada', 0),
            'federacao_nao_organizada' => data_get($dados, 'estrutura.federacao_nao_organizada', 0),
        ];
        $retorno['total_socios'] = intval($retorno['ativos']) + intval($retorno['cooperadores']);
        $retorno['total_programacoes'] = intval($retorno['social'])
            + intval($retorno['evangelistico'])
            + intval($retorno['espiritual'])
            + intval($retorno['recreativo'])
            + intval($retorno['oracao']);

        return $retorno;
    }

    public static function formatarGrafico($dados)
    {
        $retorno = [
            'perfil' => [
                'labels' => ['Ativos', 'Cooperadores', 'Homens', 'Mulheres', 'Menor de 19', 'Entre 19 e 23', 'Entre 24 e 29', 'Entre 30 e 35'],
                'datasets' => [
                    [
                        'backgroundColor' => ['#003f5c', '#2f4b7c'],
                        'data' => self::processarDadosPorcentagem([
                            $dados->perfil['ativos'] ?? 0,
                            $dados->perfil['cooperadores'] ?? 0
                        ])
                    ],
                    [
                        'backgroundColor' => ['#665191', '#a05195'],
                        'data' => self::processarDadosPorcentagem([
                            $dados->perfil['homens'] ?? 0,
                            $dados->perfil['mulheres'] ?? 0
                        ])
                    ],
                    [
                        'backgroundColor' => ['#d45087', '#f95d6a', '#ff7c43', '#ffa600'],
                        'data' => self::processarDadosPorcentagem([
                            $dados->perfil['menor19'] ?? 0,
                            $dados->perfil['de19a23'] ?? 0 ,
                            $dados->perfil['de24a29'] ?? 0 ,
                            $dados->perfil['de30a35'] ?? 0
                        ])
                    ],
                ]
            ],
            'programacoes' => [
                'labels' => ['Vigília e Oração', 'Social', 'Evangelistico/Missional', 'Espiritual', 'Recreativo' ],
                'datasets' => [
                    [
                        'data' => self::processarDadosPorcentagem([
                            $dados->programacoes['oracao'],
                            $dados->programacoes['social'],
                            $dados->programacoes['evangelistico'],
                            $dados->programacoes['espiritual'],
                            $dados->programacoes['recreativo']
                        ]),
                        'backgroundColor' => ['#003f5c','#58508d','#bc5090','#ff6361','#ffa600']
                    ]
                ]
            ],
            'escolaridade' => [
                'labels' => ['Ens. Fundamental', 'Ens. Médio', 'Ens. Técnico', 'Ens. Superior', 'Pós-Graduação' ],
                'datasets' => [
                    [
                        'data' => self::processarDadosPorcentagem([
                            $dados->escolaridade['fundamental'],
                            $dados->escolaridade['medio'],
                            $dados->escolaridade['tecnico'],
                            $dados->escolaridade['superior'],
                            $dados->escolaridade['pos']
                        ]),
                        'backgroundColor' => ['#003f5c','#58508d','#bc5090','#ff6361','#ffa600']
                    ]
                ]
            ]
        ];

        return $retorno;
    }

    private static function extrairValorAci($dados): float
    {
        $aci = $dados->aci ?? null;
        if (!is_array($aci)) {
            return 0;
        }
        $raw = $aci['valor'] ?? $aci['valor_repassado'] ?? 0;
        if (is_numeric($raw)) {
            return (float) $raw;
        }
        if (is_string($raw) && $raw !== '') {
            return \App\Helpers\FormHelper::converterParaFloat($raw);
        }
        return 0;
    }

    public static function processarDadosPorcentagem(array $dados)
    {
        try {
            $total = 0;
            foreach($dados as $valor){
                $total += $valor;
            }

            $retorno = [];

            foreach ($dados as $valor) {
                if ($total == 0) {
                    continue;
                }
                $retorno[] = floatval(number_format(($valor * 100 / $total), 2));
            }
            return $retorno;
        } catch (\Throwable $th) {
            throw new Exception("Erro ao processar dados porcentagem");
        }
    }

}
