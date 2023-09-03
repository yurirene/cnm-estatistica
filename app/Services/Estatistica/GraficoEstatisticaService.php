<?php

namespace App\Services\Estatistica;

use App\Models\Estado;
use App\Services\MapaService;
use App\Strategies\PesquisaGrafico\AbstractGrafico;

class GraficoEstatisticaService extends AbstractGrafico
{

    private static $dados = [];

    public const GRAFICOS = [
        [
            'nome' => 'tipo_socios',
            'coluna' => 'perfil',
            'titulo' => 'Nº de Sócios',
            'campos' => ['ativos', 'cooperadores'],
            'labels' => ['Ativos', 'Cooperadores'],
            'tipo' => 'pie'
        ],
        [
            'nome' => 'genero',
            'coluna' => 'perfil',
            'titulo' => 'Nº de Sócios',
            'campos' => ['homens', 'mulheres'],
            'labels' => ['Masculino', 'Feminino'],
            'tipo' => 'donut'
        ],
        [
            'nome' => 'idade',
            'coluna' => 'perfil',
            'titulo' => 'Nº de Sócios',
            'campos' => ['menor19', 'de19a23', 'de24a29', 'de30a35'],
            'labels' => ['< 19 anos', '19 a 23 anos', '24 a 29 anos', '30 a 35 anos'],
            'tipo' => 'bar'
        ],
        [
            'nome' => 'estado_civil',
            'coluna' => 'estado_civil',
            'titulo' => 'Nº de Sócios',
            'campos' => ['solteiros', 'casados', 'divorciados', 'viuvos', 'filhos'],
            'labels' => ['Solteiros', 'Casados', 'Divorciados', 'Viúvos', 'com Filhos'],
            'tipo' => 'bar'
        ],
        [
            'nome' => 'escolaridade',
            'coluna' => 'escolaridade',
            'titulo' => 'Nº de Sócios',
            'campos' => ['fundamental', 'medio', 'tecnico', 'superior', 'pos'],
            'labels' => ['Ens. Fundamental', 'Ens. Médio', 'Ens. Técnic', 'Ens. Superior', 'Pós-Graduação'],
            'tipo' => 'donut'
        ],
        [
            'nome' => 'desempregados',
            'coluna' => 'escolaridade',
            'titulo' => 'Nº de Sócios',
            'campos' => ['desempregado'],
            'labels' => ['Desempregados'],
            'tipo' => 'bar'
        ],
        [
            'nome' => 'deficiencias',
            'coluna' => 'deficiencias',
            'titulo' => 'Nº de Sócios',
            'campos' => [
                ['cegos', 'baixa_visao'],
                ['auditiva', 'surdos'],
                ['fisica_inferior', 'fisica_superior'],
                ['neurologico', 'intelectual'],
            ],
            'labels' => ['Visual', 'Auditiva', 'Física', 'Mental'],
            'labels_map' => [
                ['Cegos', 'Baixa Visão'],
                ['Auditiva (Parcial)', 'Surdos'],
                ['Física/Motora membros inferiores', 'Física/Motora membros superiores'],
                ['Neurológicos', 'Intelectual']
            ],
            'tipo' => 'groupBar'
        ],
        [
            'nome' => 'repasse_aci',
            'coluna' => 'aci',
            'titulo' => 'Quantidade',
            'campos' => [
                ['locais', 'locais_nao'],
                ['federacoes', 'federacoes_nao'],
                ['sinodais', 'sinodais_nao'],
            ],
            'labels' => [
                'UMPs Repassaram',
                'UMPs Não Repassaram',
                'Federações Repassaram',
                'Federações Não Repassaram',
                'Sinodais Repassaram',
                'Sinodais Não Repassaram'
            ],
            'tipo' => 'multiPie'
        ],
        [
            'nome' => 'programacoes_umps',
            'coluna' => 'programacoes.locais',
            'titulo' => 'Nº de Programções',
            'campos' => [
                'social',
                'oracao',
                'evangelistico',
                'espiritual',
                'recreativo',
            ],
            'labels' => [
                'Social',
                'Oração',
                'Evangelística/Missionária',
                'Espiritual',
                'Recreativa'
            ],
            'tipo' => 'bar'
        ],
        [
            'nome' => 'programacoes_sinodais',
            'coluna' => 'programacoes.sinodais',
            'titulo' => 'Nº de Programções',
            'campos' => [
                'social',
                'oracao',
                'evangelistico',
                'espiritual',
                'recreativo',
            ],
            'labels' => [
                'Social',
                'Oração',
                'Evangelística/Missionária',
                'Espiritual',
                'Recreativa'
            ],
            'tipo' => 'bar'
        ],
        [
            'nome' => 'programacoes_federacoes',
            'coluna' => 'programacoes.federacoes',
            'titulo' => 'Nº de Programções',
            'campos' => [
                'social',
                'oracao',
                'evangelistico',
                'espiritual',
                'recreativo',
            ],
            'labels' => [
                'Social',
                'Oração',
                'Evangelística/Missionária',
                'Espiritual',
                'Recreativa'
            ],
            'tipo' => 'bar'
        ],
        [
            'nome' => 'distribuicao',
        ],
    ];

    public const GRAFICOS_COMPLEXOS = [
        'deficiencias',
        'repasse_aci'
    ];

    /**
     * Função para Carregar os gráficos da área de estatística
     *
     * @param array $request
     * @return array
     */
    public static function graficos(array $request): array
    {

        $retorno = [];
        foreach (self::GRAFICOS as $grafico) {

            if (in_array($grafico['nome'], self::GRAFICOS_COMPLEXOS)) {
                $dados = self::dadosComplexos(
                    $grafico['coluna'],
                    $grafico['campos'],
                    $request,
                    $grafico['labels_map'] ?? []
                );
            } else if ($grafico['nome'] == 'distribuicao') {
                $retorno['graficos'][] = [
                    'dados' => self::getDadosDistribuicao($request),
                    'config' => [],
                    'id' => $grafico['nome']
                ];
                continue;
            } else {
                $dados = self::dados(
                    $grafico['coluna'],
                    $grafico['campos'],
                    $request
                );
            }
            $dados['label'] = $grafico['labels'];
            $dados['titulo'] = $grafico['titulo'];
            $dadosGrafico = call_user_func_array(
                [
                    self::class,
                    $grafico['tipo']
                ],
                [
                    $dados
                ]
            );
            $retorno['graficos'][] = [
                'config' => $dadosGrafico,
                'id' => $grafico['nome']
            ];
        }
        $retorno['totalizadores'] = [
            'total_sinodais' => self::$dados['estrutura']['sinodais_organizadas'],
            'total_federacoes' => self::$dados['estrutura']['federacoes_organizadas'],
            'total_umps' => self::$dados['estrutura']['umps_organizadas'],
            'total_socios' => self::$dados['perfil']['ativos'] + self::$dados['perfil']['cooperadores'],
            'relatorios_sinodais' => self::$dados['abrangencia']['sinodais']['respondido'] . ' / ' . self::$dados['abrangencia']['sinodais']['total'],
            'relatorios_federacoes' => self::$dados['abrangencia']['federacoes']['respondido'] . ' / ' . self::$dados['abrangencia']['federacoes']['total'],
            'relatorios_umps_locais' => self::$dados['abrangencia']['locais']['respondido'] . ' / ' . self::$dados['abrangencia']['locais']['total'],
            'qualidade_relatorio' => round(self::$dados['qualidade'], 2)  . "%",
        ];
        return $retorno;
    }

    /**
     * Seleciona os dados necessários a partir dos totalizadores dos formulários de umps locais
     *
     * @param string $coluna
     * @param array $campos
     * @param array $request
     * @return array
     */
    public static function dados(string $coluna, array $campos, array $request): array
    {
        if (empty(self::$dados)) {
            self::$dados = EstatisticaService::getDadosRelatorioGeral($request['ano'], $request['regiao']);
        }
        $dadosGerais = self::$dados;
        $dadosEspecificos = [];
        foreach ($campos as $campo) {
            $dadosEspecificos['dados'][$campo] = data_get($dadosGerais, $coluna.'.'.$campo);
        }
        return $dadosEspecificos;
    }



    /**
     *
     * Seleciona os dados necessários a partir dos totalizadores dos formulários de umps locais
     *
     * @param string $coluna
     * @param array $grupoCampos
     * @param array $labels_map
     * @param array $request
     * @return array
     */
    public static function dadosComplexos(string $coluna, array $grupoCampos, array $request, array $labels_map = []): array
    {
        if (empty(self::$dados)) {
            self::$dados = EstatisticaService::getDadosRelatorioGeral($request['ano'], $request['regiao']);
        }
        $dadosGerais = self::$dados;
        $dadosEspecificos = [];
        foreach ($grupoCampos as $key => $campos) {
            foreach ($campos as $k => $campo) {
                if (!empty($labels_map)) {
                    $dadosEspecificos['dados'][$key][$labels_map[$key][$k]] = $dadosGerais[$coluna][$campo];
                    continue;
                }
                $dadosEspecificos['dados'][$key][$campo] = $dadosGerais[$coluna][$campo];
            }
        }
        return $dadosEspecificos;
    }


    public static function getDadosDistribuicao(array $request): array
    {
        $estados = Estado::when(!is_null($request['regiao']), function ($sql) use ($request) {
                return $sql->where('regiao_id', $request['regiao']);
            })
            ->get()
            ->map(function ($item) {
                return 'br-' . strtolower($item->sigla);
            })->toArray();
        $data = [];
        foreach ($estados as $estado) {
            $totalizador = MapaService::getTotalizador($estado, $request['ano']);
            $data[] = [
                'hc-key' => $estado,
                'n_socios' => $totalizador['n_socios'],
                'n_umps' => $totalizador['n_umps'],
                'n_federacoes' => $totalizador['n_federacoes']
            ];
        }
        return $data;
    }


    /**
     * Gerador de Gráfico do Tipo Pizza
     *
     * @param array $dados
     * @return array
     */
    public static function pie(array $dados): array
    {
        try {
            return [
                "type" => 'pie',
                "data" => [
                    "labels" => $dados['label'],
                    "datasets" => [
                        [
                            "label" => $dados['titulo'],
                            "data" => array_values($dados['dados']),
                            "backgroundColor" => self::getPaleta($dados),
                        ]
                    ]
                ],
                "options" => [
                    "responsive" => true,
                    "plugins" => [
                        "legend" => [
                            "position" => 'top',
                        ],
                        "title" => [
                            "display" => false,
                        ]
                    ]
                ],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Gerador de Gráfico do Tipo Pizza Multi Data
     *
     * @param array $dados
     * @return array
     */
    public static function multiPie(array $dados): array
    {
        try {
            $retorno = [
                "type" => 'pie',
                "data" => [
                    "labels" => $dados['label'],
                    "datasets" => []
                ],
                "options" => [
                    "responsive" => true,
                    "plugins" => [
                        "legend" => [
                            "position" => 'top',
                        ],
                        "title" => [
                            "display" => false,
                        ]
                    ]
                ],
                'need' => true
            ];
            foreach ($dados['dados'] as $dado) {
                $retorno['data']['datasets'][] =  [
                    "data" => array_values($dado),
                    "backgroundColor" => self::getPaleta(['dados' => $dado]),
                ];
            }
            return $retorno;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Gerador de Gráfico do Tipo Donut
     *
     * @param array $dados
     * @return array
     */
    public static function donut(array $dados): array
    {
        try {
            return [
                "type" => 'doughnut',
                "data" => [
                    "labels" => $dados['label'],
                    "datasets" => [
                        [
                            "label" => $dados['titulo'],
                            "data" => array_values($dados['dados']),
                            "backgroundColor" => self::getPaleta($dados),
                        ]
                    ]
                ],
                "options" => [
                    "responsive" => true,
                    "plugins" => [
                        "legend" => [
                            "position" => 'top',
                        ],
                        "title" => [
                            "display" => false,
                        ]
                    ]
                ],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Gerador de Gráfico do Tipo Barras Horizontais
     *
     * @param array $dados
     * @return array
     */
    public static function horizontalBar(array $dados): array
    {
        try {
            return [
                "type" => 'bar',
                "data" => [
                    "labels" => $dados['label'],
                    "datasets" => [
                        [
                            "label" => $dados['titulo'],
                            "data" => array_values($dados['dados']),
                            "backgroundColor" => self::getPaleta($dados),
                        ]
                    ]
                ],
                "options" => [
                    "indexAxis" => 'y',
                    "responsive" => true,
                    "plugins" => [
                        "legend" => [
                            "position" => 'top',
                        ],
                        "title" => [
                            "display" => false,
                        ]
                    ]
                ],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Gerador de Gráfico do Tipo Barras Verticais
     *
     * @param array $dados
     * @return array
     */
    public static function bar(array $dados): array
    {
        try {
            return [
                "type" => 'bar',
                "data" => [
                    "labels" => $dados['label'],
                    "datasets" => [
                        [
                            "label" => $dados['titulo'],
                            "data" => array_values($dados['dados']),
                            "borderRadius" => 15,
                            "backgroundColor" => self::getPaleta($dados),
                        ]
                    ]
                ],
                "options" => [
                    "responsive" => true,
                    "plugins" => [
                        "legend" => [
                            "position" => 'top',
                        ],
                        "title" => [
                            "display" => false,
                        ]
                    ]
                ],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Gerador de Gráfico do Tipo Barras Agrupadas
     *
     * @param array $dados
     * @return array
     */
    public static function groupBar(array $dados): array
    {
        try {
            $retorno = [
                "type" => 'bar',
                "data" => [
                    "labels" => [],
                    "datasets" => []
                ],
                "options" => [
                    "responsive" => true,
                    "plugins" => [
                        "legend" => [
                            "position" => 'top',
                        ],
                        "title" => [
                            "display" => false,
                        ]
                    ],
                    "scales" => [
                        "x" => [
                          "stacked" => true,
                        ],
                        "y" => [
                          "stacked" => true
                        ]
                    ]
                ],
            ];
            foreach ($dados['dados'] as $key => $dado) {
                $retorno['data']["datasets"][] = [
                    "label" => $dados['label'][$key],
                    "data" => $dado,
                    "borderRadius" => 15,
                    "backgroundColor" => self::getPaleta(['dados' => $dado]),
                ];
            }
            return $retorno;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
