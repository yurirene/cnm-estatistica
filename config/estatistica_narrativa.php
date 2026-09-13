<?php

return [
    'textos' => [
        'crescimento_forte' => [
            'Observa-se crescimento expressivo de {percentual}% em {indicador_nome}.',
            '{indicador_nome} avançou {percentual}% em relação ao ano anterior, um dos maiores crescimentos do período.',
            'Houve alta relevante de {percentual}% em {indicador_nome} neste ciclo.',
        ],
        'crescimento_moderado' => [
            'Os dados sugerem crescimento moderado de {percentual}% em {indicador_nome}.',
            '{indicador_nome} registrou alta de {percentual}% em relação ao ano anterior.',
        ],
        'estabilidade' => [
            'Observa-se estabilidade em {indicador_nome} neste ciclo, com variação de {percentual}%.',
            '{indicador_nome} permaneceu em patamar semelhante ao ano anterior.',
        ],
        'retracao_leve' => [
            'Os dados sugerem retração leve de {percentual}% em {indicador_nome}.',
            '{indicador_nome} recuou {percentual}% em relação ao ano anterior.',
        ],
        'retracao_forte' => [
            'Observa-se retração relevante de {percentual}% em {indicador_nome}.',
            '{indicador_nome} apresentou queda acentuada de {percentual}% neste ciclo.',
        ],
        'recuperacao_pos_retracao' => [
            'Após retração em {ano_anterior}, houve recuperação em {ano_atual}.',
            'O indicador se recuperou em {ano_atual}, revertendo a queda observada em {ano_anterior}.',
        ],
        'crescimento_indeterminado' => [
            'Não é possível calcular a variação percentual de {indicador_nome}: o valor anterior é zero.',
            'A variação de {indicador_nome} permanece indeterminada neste ciclo por ausência de base no ano anterior.',
        ],
        'concentracao_faixa_jovem' => [
            'Observa-se concentração relevante nas faixas etárias mais jovens.',
            'Os dados sugerem predominância das faixas mais jovens no perfil demográfico.',
        ],
        'concentracao_faixa_adulta' => [
            'Observa-se concentração relevante nas faixas etárias adultas.',
            'Os dados sugerem predominância das faixas adultas no perfil demográfico.',
        ],
        'distribuicao_equilibrada' => [
            'A distribuição etária apresenta-se relativamente equilibrada entre as faixas.',
            'Nenhuma faixa etária concentra, isoladamente, a maior parte do perfil.',
        ],
        'renovacao_alta' => [
            'O índice de renovação geracional está em {indice}%, o que sugere alta renovação.',
            'Observa-se renovação geracional elevada ({indice}%) neste ciclo.',
        ],
        'renovacao_baixa' => [
            'O índice de renovação geracional está em {indice}%, abaixo da faixa de referência.',
            'Os dados sugerem baixa renovação geracional ({indice}%) neste período.',
        ],
        'alta_diversidade_atividades' => [
            'Foram registradas atividades em {categorias} categorias, o que sugere alta diversidade.',
            'A programação aparece distribuída em {categorias} categorias distintas.',
        ],
        'baixa_diversidade_atividades' => [
            'As atividades concentram-se em {categorias} categorias, o que sugere baixa diversidade.',
            'Observa-se pouca variedade de categorias na programação registrada.',
        ],
        'concentracao_categoria_atividade' => [
            'Uma categoria concentra {percentual}% do total de atividades.',
            'Observa-se concentração relevante em uma única categoria de atividades ({percentual}%).',
        ],
        'aumento_atividades' => [
            'O total de atividades avançou {percentual}% em relação ao ano anterior.',
            'Observa-se aumento de {percentual}% no volume de atividades neste ciclo.',
        ],
        'queda_atividades' => [
            'O total de atividades recuou {percentual}% em relação ao ano anterior.',
            'Observa-se queda de {percentual}% no volume de atividades neste ciclo.',
        ],
        'acima_mediana_porte' => [
            'A posição relativa está acima da mediana {grupo_pares}.',
            'Os dados situam o indicador acima da mediana {grupo_pares}.',
        ],
        'abaixo_mediana_porte' => [
            'Abaixo da mediana {grupo_pares}.',
            'Posição relativa abaixo da mediana {grupo_pares}.',
        ],
        'na_mediana_porte' => [
            'A posição relativa está na faixa da mediana {grupo_pares}.',
            'O comparativo sugere um patamar próximo à mediana {grupo_pares}.',
        ],
        'mudanca_relevante_posicao' => [
            'Houve mudança relevante de posição relativa em relação ao ano anterior.',
            'Observa-se salto de faixa no comparativo {grupo_pares} entre {ano_anterior} e {ano_atual}.',
        ],
        'anomalia_detectada' => [
            'Foi identificada variação relevante em relação ao histórico, o que pode representar mudança real ou diferença no preenchimento.',
            'Os dados apontam {quantidade} sinal(is) atípico(s) neste ciclo, a investigar com cautela.',
        ],
        'qualidade_alta' => [
            'Os dados apresentam alta completude, com score de {score}.',
            'A qualidade dos dados neste ciclo é alta (score {score}).',
        ],
        'qualidade_media' => [
            'A qualidade dos dados situa-se em nível intermediário, com score de {score}.',
            'Observa-se qualidade mediana dos dados (score {score}).',
        ],
        'qualidade_baixa' => [
            'A qualidade dos dados está abaixo da faixa de referência, com score de {score}.',
            'Os dados sugerem baixa completude neste ciclo (score {score}).',
        ],
        'tendencia_alta_consistente' => [
            'Os dados indicam tendência de alta consistente ao longo dos últimos anos.',
            'Observa-se sequência de altas em {indicador_nome} no histórico recente.',
        ],
        'tendencia_queda_consistente' => [
            'Os dados indicam tendência de queda consistente ao longo dos últimos anos.',
            'Observa-se sequência de recuos em {indicador_nome} no histórico recente.',
        ],
        'tendencia_oscilante' => [
            'O histórico recente de {indicador_nome} apresenta sinais alternados.',
            'Os dados sugerem tendência oscilante, sem direção única nos últimos anos.',
        ],
    ],
    'perguntas' => [
        [
            'eventos' => ['crescimento_forte', 'recuperacao_pos_retracao'],
            'texto' => 'A recuperação de {ano_atual} se repete em outras UMPs da Federação?',
            'ordem' => 1,
        ],
        [
            'eventos' => ['renovacao_alta', 'renovacao_baixa'],
            'texto' => 'Quais características possuem as UMPs que tiveram {evento_textual} no mesmo período?',
            'ordem' => 2,
        ],
        [
            'eventos' => ['aumento_atividades', 'concentracao_categoria_atividade'],
            'texto' => 'A distribuição das atividades mudou em relação aos anos anteriores?',
            'ordem' => 3,
        ],
    ],
];
