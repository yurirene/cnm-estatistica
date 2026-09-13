<?php

namespace App\Services\Gamificacao\Enums;

enum OrigemAuditoria: string
{
    case Formulario = 'formulario';
    case Aci = 'aci';
    case Ce = 'ce';
    case Comando = 'comando';
    case Conquista = 'conquista';
}
