<?php

namespace App\Services\Estatistica;

use App\Models\Estado;
use App\Services\MapaService;
use App\Strategies\PesquisaGrafico\AbstractGrafico;

class GraficoEstatisticaService extends AbstractGrafico
{

    private static $dados = [];

    public const CORES = [
        'umps' => '#2E9E8F',
        'fed' => '#8B2E45',
        'sin' => '#1B2A63',
        'blue' => '#2E43A0',
        'pale' => '#DCE0F5',
        'gold' => '#D9A441',
        'sand' => '#D9C9A3',
        'gray' => '#8890B0',
    ];

    public const GRAFICOS = [
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
            'nome' => 'deficiencias',
            'tipo' => 'deficienciasBar'
        ],
        [
            'nome' => 'repasse_aci',
            'tipo' => 'aciGrouped'
        ],
        [
            'nome' => 'programacoes',
            'tipo' => 'programacoesStacked'
        ],
        [
            'nome' => 'distribuicao',
        ],
    ];

    /**
     * Função para Carregar os gráficos da área de estatística
     *
     * @param array $request
     * @return array
     */
    public static function graficos(array $request): array
    {
        $request = self::normalizarFiltro($request);
        self::$dados = EstatisticaService::getDadosRelatorioGeral($request['ano'], $request['regiao']);

        $retorno = ['graficos' => []];
        foreach (self::GRAFICOS as $grafico) {
            if ($grafico['nome'] == 'distribuicao') {
                $retorno['graficos'][] = [
                    'dados' => self::getDadosDistribuicao($request),
                    'config' => [],
                    'id' => $grafico['nome']
                ];
                continue;
            }

            if (($grafico['tipo'] ?? '') === 'deficienciasBar') {
                $retorno['graficos'][] = [
                    'config' => self::deficienciasBar(),
                    'id' => $grafico['nome'],
                ];
                continue;
            }
            if (($grafico['tipo'] ?? '') === 'aciGrouped') {
                $retorno['graficos'][] = [
                    'config' => self::aciGrouped(),
                    'id' => $grafico['nome'],
                ];
                continue;
            }
            if (($grafico['tipo'] ?? '') === 'programacoesStacked') {
                $retorno['graficos'][] = [
                    'config' => self::programacoesStacked(),
                    'id' => $grafico['nome'],
                ];
                continue;
            }

            $dados = self::dados(
                $grafico['coluna'],
                $grafico['campos'],
                $request
            );
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

        $umps = (int) (self::$dados['estrutura']['umps_organizadas'] ?? 0);
        $retorno['totalizadores'] = [
            'total_sinodais' => self::$dados['estrutura']['sinodais_organizadas'],
            'total_federacoes' => self::$dados['estrutura']['federacoes_organizadas'],
            'total_umps' => self::$dados['estrutura']['umps_organizadas'],
            'total_socios' => (self::$dados['perfil']['ativos'] ?? 0) + (self::$dados['perfil']['cooperadores'] ?? 0),
            'relatorios_sinodais' => self::$dados['abrangencia']['sinodais']['respondido'] . ' / ' . self::$dados['abrangencia']['sinodais']['total'],
            'relatorios_federacoes' => self::$dados['abrangencia']['federacoes']['respondido'] . ' / ' . self::$dados['abrangencia']['federacoes']['total'],
            'relatorios_umps_locais' => self::$dados['abrangencia']['locais']['respondido'] . ' / ' . self::$dados['abrangencia']['locais']['total'],
            'qualidade_relatorio' => ($umps > 0 ? round((float) self::$dados['qualidade'], 1) : 0) . '%',
        ];

        return array_merge($retorno, PainelNacionalService::complemento($request, self::$dados));
    }

    public static function normalizarFiltro(array $request): array
    {
        $ano = (int) ($request['ano'] ?? EstatisticaService::getAnoReferencia());
        $regiao = $request['regiao'] ?? null;
        if ($regiao === '' || $regiao === null) {
            $regiao = null;
        } else {
            $regiao = (int) $regiao;
            $regiao = $regiao > 0 ? $regiao : null;
        }

        return [
            'ano' => $ano,
            'regiao' => $regiao,
        ];
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

    public static function deficienciasBar(): array
    {
        $grupos = [
            ['Visual', ['cegos', 'baixa_visao'], self::CORES['blue']],
            ['Auditiva', ['auditiva', 'surdos'], self::CORES['umps']],
            ['Física', ['fisica_inferior', 'fisica_superior'], self::CORES['gold']],
            ['Neuro/Intelectual', ['neurologico', 'intelectual'], self::CORES['fed']],
        ];
        $labels = [];
        $valores = [];
        $cores = [];
        foreach ($grupos as $grupo) {
            $labels[] = $grupo[0];
            $total = 0;
            foreach ($grupo[1] as $campo) {
                $total += (int) (self::$dados['deficiencias'][$campo] ?? 0);
            }
            $valores[] = $total;
            $cores[] = $grupo[2];
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Sócios',
                    'data' => $valores,
                    'backgroundColor' => $cores,
                    'borderRadius' => 6,
                    'maxBarThickness' => 70,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['display' => false],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                    'x' => ['grid' => ['display' => false]],
                ],
            ],
        ];
    }

    public static function aciGrouped(): array
    {
        $aci = self::$dados['aci'] ?? [];

        return [
            'type' => 'bar',
            'data' => [
                'labels' => ['UMPs Locais', 'Federações', 'Sinodais'],
                'datasets' => [
                    [
                        'label' => 'Repassaram',
                        'data' => [
                            (int) ($aci['locais'] ?? 0),
                            (int) ($aci['federacoes'] ?? 0),
                            (int) ($aci['sinodais'] ?? 0),
                        ],
                        'backgroundColor' => self::CORES['umps'],
                        'borderRadius' => 5,
                    ],
                    [
                        'label' => 'Não repassaram',
                        'data' => [
                            (int) ($aci['locais_nao'] ?? 0),
                            (int) ($aci['federacoes_nao'] ?? 0),
                            (int) ($aci['sinodais_nao'] ?? 0),
                        ],
                        'backgroundColor' => self::CORES['pale'],
                        'borderRadius' => 5,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                    'x' => ['grid' => ['display' => false]],
                ],
            ],
        ];
    }

    public static function programacoesStacked(): array
    {
        $niveis = [
            'Sinodais' => self::$dados['programacoes']['sinodais'] ?? [],
            'Federações' => self::$dados['programacoes']['federacoes'] ?? [],
            'UMPs Locais' => self::$dados['programacoes']['locais'] ?? [],
        ];
        $categorias = [
            'social' => 'Social',
            'oracao' => 'Oração',
            'evangelistico' => 'Evang./Miss.',
            'espiritual' => 'Espiritual',
            'recreativo' => 'Recreativa',
        ];
        $cores = [
            self::CORES['fed'],
            self::CORES['sin'],
            self::CORES['gold'],
            self::CORES['umps'],
            self::CORES['blue'],
        ];
        $labels = array_keys($niveis);
        $datasets = [];
        $i = 0;
        foreach ($categorias as $chave => $label) {
            $data = [];
            foreach ($niveis as $row) {
                $soma = array_sum(array_map('intval', $row));
                $valor = (int) ($row[$chave] ?? 0);
                $data[] = $soma > 0 ? round(($valor * 100) / $soma, 1) : 0;
            }
            $datasets[] = [
                'label' => $label,
                'data' => $data,
                'backgroundColor' => $cores[$i],
            ];
            $i++;
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                ],
                'scales' => [
                    'x' => [
                        'stacked' => true,
                        'max' => 100,
                        'ticks' => [
                            'callback' => null,
                        ],
                    ],
                    'y' => [
                        'stacked' => true,
                        'grid' => ['display' => false],
                    ],
                ],
            ],
            'percentTicks' => true,
        ];
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
                    "maintainAspectRatio" => false,
                    "cutout" => '68%',
                    "plugins" => [
                        "legend" => [
                            "position" => 'bottom',
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
