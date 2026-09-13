<?php

namespace App\Services\EstatisticaInteligente\Enums;

enum StatusAnaliseEnum: string
{
    case Pendente = 'pendente';
    case Processando = 'processando';
    case Concluida = 'concluida';
    case Erro = 'erro';
}
