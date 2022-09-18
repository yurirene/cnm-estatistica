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
            'desempregado' => $dados->escolaridade['desempregado'] ?? 0,
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
        ];
        if ($dados->programacoes) {
            $retorno['social'] = $dados->programacoes['social'] ?? 0;
            $retorno['evangelistico'] = $dados->programacoes['evangelistico'] ?? 0;
            $retorno['espiritual'] = $dados->programacoes['espiritual'] ?? 0;
            $retorno['recreativo'] = $dados->programacoes['recreativo'] ?? 0;
            $retorno['oracao'] = $dados->programacoes['oracao'] ?? 0;
        }
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