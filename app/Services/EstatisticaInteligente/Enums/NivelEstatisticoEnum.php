<?php

namespace App\Services\EstatisticaInteligente\Enums;

enum NivelEstatisticoEnum: string
{
    case Local = 'local';
    case Federacao = 'federacao';
    case Sinodal = 'sinodal';
    case Regiao = 'regiao';
    case Nacional = 'nacional';
}
