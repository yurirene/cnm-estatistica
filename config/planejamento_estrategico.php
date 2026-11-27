<?php

/**
 * Parâmetros do Planejamento Estratégico 2026–2030 da UMP.
 *
 * Os indicadores definem como comparar os dados estatísticos registrados
 * (e outras participações, como Comissão Executiva) com as metas do quinquênio.
 *
 * Tipos de cálculo suportados:
 * - percentual_socios_campo: (soma do campo / total de sócios) * 100
 * - percentual_socios_campos_soma: (soma de vários campos / total de sócios) * 100
 * - percentual_umps_com_acao: % de UMPs locais com ao menos 1 ação nos campos informados
 * - percentual_sinodais_discipulado: % de sinodais com prática ativa (limiar configurável)
 * - percentual_instancias_gtsi: % de instâncias cumprindo pilares GTSI (estatística, ACI, CE)
 * - percentual_sinodais_ce: % de sinodais com presença confirmada na CE do ano
 */
return [
    'ciclo' => '2026-2030',
    'ano_inicio' => 2026,
    'ano_fim' => 2030,
    'titulo' => 'Planejamento Estratégico 2026–2030',
    'subtitulo' => 'Acompanhamento das metas nacionais da UMP',

    'missao' => 'Unir e mobilizar a Mocidade Presbiteriana em um propósito santo de maturidade e serviço, para a glória de Deus e edificação da sociedade.',
    'visao' => 'Até 2030, ser uma UMP nacionalmente reconhecida por discipular com profundidade bíblica, evangelizar com coragem e relevância, acolher e integrar novos jovens de forma intencional, e multiplicar líderes servos em todas as regiões do país.',

    'fases' => [
        2026 => 'Padronização',
        2027 => 'Expansão',
        2028 => 'Consolidação',
        2029 => 'Sucessão',
        2030 => 'Avaliação e Transição',
    ],

    /**
     * Limiares de status em relação ao percentual da meta do ano selecionado.
     * progresso_relativo = (valor_atual / meta_do_ano) * 100
     */
    'status' => [
        'no_prazo' => ['min' => 90, 'label' => 'No prazo', 'classe' => 'on-track'],
        'em_progresso' => ['min' => 70, 'label' => 'Em progresso', 'classe' => 'on-track'],
        'atencao' => ['min' => 50, 'label' => 'Atenção', 'classe' => 'at-risk'],
        'risco' => ['min' => 0, 'label' => 'Risco', 'classe' => 'off-track'],
    ],

    'objetivos' => [
        [
            'id' => 1,
            'titulo' => 'Implementar uma cultura de discipulado bíblico que gera maturidade e multiplicação',
            'meta_cnm' => '70% das sinodais com prática de discipulado implementada e ativa',
            'cor' => 'teal',
            'indicadores' => [
                [
                    'chave' => 'sinodais_discipulado_ativo',
                    'label' => 'Sinodais com discipulado ativo',
                    'descricao' => 'Percentual de sinodais com prática de discipulado implementada e ativa',
                    'unidade' => '%',
                    'principal' => true,
                    'tipo' => 'percentual_sinodais_discipulado',
                    'parametros' => [
                        // Sinodal conta como "ativa" se, entre as UMPs que reportaram,
                        // o % de sócios sendo discipulados for >= este limiar.
                        'limiar_percentual_sendo_discipulados' => 20,
                        'campo_discipulado' => 'sendo_discipulados',
                        'minimo_umps_reportando' => 1,
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 5,
                        2027 => 15,
                        2028 => 30,
                        2029 => 50,
                        2030 => 70,
                    ],
                ],
                [
                    'chave' => 'jovens_sendo_discipulados',
                    'label' => 'Jovens sendo discipulados',
                    'descricao' => 'Percentual de sócios sendo discipulados',
                    'unidade' => '%',
                    'principal' => false,
                    'tipo' => 'percentual_socios_campo',
                    'parametros' => [
                        'campo' => 'discipulado.sendo_discipulados',
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 20,
                        2027 => 30,
                        2028 => 45,
                        2029 => 60,
                        2030 => 70,
                    ],
                ],
                [
                    'chave' => 'jovens_discipuladores',
                    'label' => 'Jovens discipuladores',
                    'descricao' => 'Percentual de sócios discipulando (método CNM + outros)',
                    'unidade' => '%',
                    'principal' => false,
                    'tipo' => 'percentual_socios_campos_soma',
                    'parametros' => [
                        'campos' => [
                            'discipulado.discipulando_cnm',
                            'discipulado.discipulando_outro',
                        ],
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 20,
                        2027 => 30,
                        2028 => 45,
                        2029 => 60,
                        2030 => 70,
                    ],
                ],
                [
                    'chave' => 'trilha_cnm',
                    'label' => 'Trilha CNM',
                    'descricao' => 'Percentual de jovens treinados na trilha da CNM',
                    'unidade' => '%',
                    'principal' => false,
                    'tipo' => 'percentual_socios_campo',
                    'parametros' => [
                        'campo' => 'discipulado.trilha_cnm',
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 5,
                        2027 => 15,
                        2028 => 25,
                        2029 => 40,
                        2030 => 50,
                    ],
                ],
            ],
        ],
        [
            'id' => 2,
            'titulo' => 'Expandir o Alcance Evangelístico e Social',
            'meta_cnm' => '100% das UMPs Locais com ao menos 1 ação de cunho evangelístico e/ou social no ano',
            'cor' => 'primary',
            'indicadores' => [
                [
                    'chave' => 'umps_acoes_evangelisticas',
                    'label' => 'UMPs com ações evangelísticas/sociais',
                    'descricao' => 'Percentual de UMPs Locais com ao menos uma ação evangelística e/ou social no ano',
                    'unidade' => '%',
                    'principal' => true,
                    'tipo' => 'percentual_umps_com_acao',
                    'parametros' => [
                        'campos_programacao' => ['evangelistico', 'social'],
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 20,
                        2027 => 40,
                        2028 => 60,
                        2029 => 80,
                        2030 => 100,
                    ],
                ],
            ],
        ],
        [
            'id' => 3,
            'titulo' => 'Aprimorar o Desenvolvimento Organizacional',
            'meta_cnm' => '80% das Sinodais cumprindo compromissos do GTSI (Anuidade, Estatística e Representatividade)',
            'cor' => 'warn',
            'indicadores' => [
                [
                    'chave' => 'sinodais_gtsi',
                    'label' => 'Sinodais cumprindo GTSI',
                    'descricao' => 'Percentual de sinodais com Anuidade, Estatística e participação na CE',
                    'unidade' => '%',
                    'principal' => true,
                    'tipo' => 'percentual_instancias_gtsi',
                    'parametros' => [
                        'nivel' => 'sinodal',
                        'pilares' => ['estatistica', 'aci', 'comissao_executiva'],
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 60,
                        2027 => 65,
                        2028 => 72,
                        2029 => 77,
                        2030 => 80,
                    ],
                ],
                [
                    'chave' => 'federacoes_gtsi',
                    'label' => 'Federações cumprindo GTSI',
                    'descricao' => 'Percentual de federações com Anuidade e Estatística entregues',
                    'unidade' => '%',
                    'principal' => false,
                    'tipo' => 'percentual_instancias_gtsi',
                    'parametros' => [
                        'nivel' => 'federacao',
                        'pilares' => ['estatistica', 'aci'],
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 60,
                        2027 => 65,
                        2028 => 72,
                        2029 => 77,
                        2030 => 80,
                    ],
                ],
                [
                    'chave' => 'umps_gtsi',
                    'label' => 'UMPs Locais cumprindo GTSI',
                    'descricao' => 'Percentual de UMPs Locais com Anuidade e Estatística entregues',
                    'unidade' => '%',
                    'principal' => false,
                    'tipo' => 'percentual_instancias_gtsi',
                    'parametros' => [
                        'nivel' => 'local',
                        'pilares' => ['estatistica', 'aci'],
                    ],
                    'baseline' => 0,
                    'metas_anuais' => [
                        2026 => 60,
                        2027 => 65,
                        2028 => 72,
                        2029 => 77,
                        2030 => 80,
                    ],
                ],
            ],
        ],
    ],

    /**
     * Participações e compromissos complementares (ex.: CE),
     * acompanhados à parte dos objetivos estratégicos.
     */
    'participacoes' => [
        [
            'chave' => 'comissao_executiva',
            'label' => 'Comissão Executiva',
            'descricao' => 'Percentual de sinodais com presença confirmada na Reunião Anual da Comissão Executiva',
            'unidade' => '%',
            'tipo' => 'percentual_sinodais_ce',
            'parametros' => [],
            'baseline' => 0,
            'metas_anuais' => [
                2026 => 70,
                2027 => 75,
                2028 => 80,
                2029 => 85,
                2030 => 90,
            ],
        ],
    ],
];
