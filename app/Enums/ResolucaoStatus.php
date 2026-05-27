<?php

namespace App\Enums;

enum ResolucaoStatus: string
{
    case Pendente = 'pendente';
    case EmAndamento = 'em_andamento';
    case Concluido = 'concluido';
    case Cancelado = 'cancelado';
}
