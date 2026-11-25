<?php

return [
    'ciclo' => '2026-2030',
    'ciclo_inicio' => 2026,
    'ciclo_fim' => 2029,
    'anos_ciclo' => 4,

    'prazo_estatistica' => ['mes' => 3, 'dia' => 1],
    'prazo_aci' => ['mes' => 3, 'dia' => 1],
    'prazo_entrega_antecipada' => ['mes' => 1, 'dia' => 10],

    'ligas' => [
        'sinodal' => [
            'simonton' => ['min' => 300, 'label' => 'Liga Simonton', 'faixa' => '300+ sócios ativos'],
            'blackford' => ['min' => 150, 'max' => 299, 'label' => 'Liga Blackford', 'faixa' => 'faixa 150–299'],
            'conceicao' => ['max' => 149, 'label' => 'Liga Conceição (JMC)', 'faixa' => 'até 149 sócios ativos'],
        ],
        'federacao' => [
            'simonton' => ['min' => 85, 'label' => 'Liga Simonton', 'faixa' => '85+ sócios ativos'],
            'blackford' => ['min' => 45, 'max' => 84, 'label' => 'Liga Blackford', 'faixa' => 'faixa 45–84'],
            'conceicao' => ['max' => 44, 'label' => 'Liga Conceição (JMC)', 'faixa' => 'até 44 sócios ativos'],
        ],
    ],

    'tetos' => [
        'ano_inicial' => 50,
        'sinodal' => 110,
        'federacao' => 100,
        'ciclo_sinodal' => 380,
        'ciclo_federacao' => 350,
    ],

    'descontos' => [
        'sinodal' => [
            100 => 323,
            75 => 266,
            50 => 209,
        ],
        'federacao' => [
            100 => 298,
            75 => 245,
            50 => 193,
        ],
    ],

    'pilares' => [
        'estatistica' => [
            'label' => 'Estatística',
            'max' => 25,
            'legais' => [0, 25],
            'minimo_percentual' => 80,
        ],
        'aci' => [
            'label' => 'Anuidade — ACI',
            'max' => 25,
            'legais' => [0, 25],
            'minimo_percentual' => 80,
        ],
        'evangelismo' => [
            'label' => 'Evangelismo e Ação Social',
            'max' => 30,
            'legais' => [0, 15, 25, 30],
            'faixas' => [
                100 => 30,
                80 => 25,
                60 => 15,
            ],
        ],
        'comissao_executiva' => [
            'label' => 'Comissão Executiva',
            'max' => 20,
            'legais' => [0, 20],
        ],
        'conexao_missionaria' => [
            'label' => 'Conexão Missionária',
            'max' => 10,
            'legais' => [0, 10],
        ],
        'entrega_antecipada' => [
            'label' => 'Bônus de Entrega Antecipada',
            'max' => 10,
            'legais' => [0, 10],
        ],
        'eventos' => [
            'label' => 'Eventos e Parcerias',
            'max' => 10,
            'legais' => range(0, 10),
            'esporadico_maximo_pontos' => 4,
        ],
    ],

    'conquistas' => [
        'conexao_missionaria' => ['pontos' => 10, 'natureza' => 'sinodal', 'unico_ano' => true],
        'pmf_oficial' => ['pontos' => 2, 'natureza' => 'federacao', 'unico_ano' => true],
        'pmf_parceria' => ['pontos' => 2, 'natureza' => 'federacao', 'unico_ano' => true],
        'djp' => ['pontos' => 2, 'natureza' => 'federacao', 'unico_ano' => true],
        'esporadico' => ['pontos' => 2, 'natureza' => 'federacao', 'unico_ano' => false],
        'selo_entrega_antecipada' => ['pontos' => 0, 'natureza' => 'federacao', 'unico_ano' => true],
        'selo_engajamento_100' => ['pontos' => 0, 'natureza' => 'sinodal', 'unico_ano' => true],
    ],
];
