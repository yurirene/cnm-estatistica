<?php

namespace App\Services\EstatisticaInteligente\Enums;

enum SeveridadeInsightEnum: string
{
    case Informativo = 'informativo';
    case Positivo = 'positivo';
    case Atencao = 'atencao';
    case Critico = 'critico';
}
