<?php

namespace App\Services\Gamificacao\Enums;

enum Liga: string
{
    case Ouro = 'ouro';
    case Prata = 'prata';
    case Bronze = 'bronze';

    public function label(): string
    {
        return match ($this) {
            self::Ouro => 'Liga Ouro',
            self::Prata => 'Liga Prata',
            self::Bronze => 'Liga Bronze',
        };
    }
}
