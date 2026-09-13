<?php

namespace App\Services\EstatisticaInteligente\Enums;

enum IntencaoPerguntaEnum: string
{
    case Historico = 'historico';
    case Comparativo = 'comparativo';
    case Crescimento = 'crescimento';
    case Atividades = 'atividades';
    case Desconhecida = 'desconhecida';
}
