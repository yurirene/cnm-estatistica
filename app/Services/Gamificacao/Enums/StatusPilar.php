<?php

namespace App\Services\Gamificacao\Enums;

enum StatusPilar: string
{
    case Ok = 'ok';
    case Risco = 'risco';
    case Pendente = 'pendente';
    case Indisponivel = 'indisponivel';
    case Disponivel = 'disponivel';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'Concluído',
            self::Risco => 'Em risco',
            self::Pendente => 'Pendente',
            self::Indisponivel => 'Indisponível',
            self::Disponivel => 'Disponível',
        };
    }
}
