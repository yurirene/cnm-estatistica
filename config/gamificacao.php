<?php

return [
    'ciclo' => '2026-2030',
    'ciclo_inicio' => 2026,
    'ciclo_fim' => 2029,
    'anos_ciclo' => 4,

    'prazo_estatistica' => ['mes' => 3, 'dia' => 1],
    'prazo_aci' => ['mes' => 3, 'dia' => 1],
    'prazo_speed_run' => ['mes' => 1, 'dia' => 10],

    'ligas' => [
        'sinodal' => [
            'ouro' => ['min' => 300, 'label' => 'Liga Ouro', 'faixa' => '300+ sócios ativos'],
            'prata' => ['min' => 150, 'max' => 299, 'label' => 'Liga Prata', 'faixa' => 'faixa 150–299'],
            'bronze' => ['max' => 149, 'label' => 'Liga Bronze', 'faixa' => 'até 149 sócios ativos'],
        ],
        'federacao' => [
            'ouro' => ['min' => 85, 'label' => 'Liga Ouro', 'faixa' => '85+ sócios ativos'],
            'prata' => ['min' => 45, 'max' => 84, 'label' => 'Liga Prata', 'faixa' => 'faixa 45–84'],
            'bronze' => ['max' => 44, 'label' => 'Liga Bronze', 'faixa' => 'até 44 sócios ativos'],
        ],
    ],

    'tetos' => [
        'sinodal' => 110,
        'federacao' => 107,
        'ciclo_sinodal' => 440,
        'ciclo_federacao' => 428,
    ],

    'descontos' => [
        'sinodal' => [
            100 => 374,
            75 => 300,
            50 => 220,
        ],
        'federacao' => [
            100 => 361,
            75 => 296,
            50 => 236,
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
            'minimo_percentual' => 60,
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
        'bonus_missionario' => [
            'label' => 'Bônus Missionário',
            'max' => 10,
            'legais' => [0, 10],
        ],
        'speed_run' => [
            'label' => 'Speed Run — Estatística',
            'max' => 10,
            'legais' => [0, 10],
        ],
        'eventos' => [
            'label' => 'Eventos e Parcerias',
            'max' => 17,
            'legais' => range(0, 17),
        ],
        'resgate' => [
            'label' => 'Pontuação de Resgate',
            'max' => 15,
            'legais' => [0, 15],
            'maximo_percentual_evangelismo' => 60,
        ],
    ],

    'conquistas' => [
        'bonus_missionario' => ['pontos' => 10, 'natureza' => 'sinodal', 'unico_ano' => true],
        'resgate' => ['pontos' => 15, 'natureza' => 'federacao', 'unico_ano' => true],
        'pmf_oficial' => ['pontos' => 5, 'natureza' => 'federacao', 'unico_ano' => true],
        'pmf_parceria' => ['pontos' => 5, 'natureza' => 'federacao', 'unico_ano' => true],
        'djp' => ['pontos' => 5, 'natureza' => 'federacao', 'unico_ano' => true],
        'esporadico' => ['pontos' => 2, 'natureza' => 'federacao', 'unico_ano' => false],
    ],
];
