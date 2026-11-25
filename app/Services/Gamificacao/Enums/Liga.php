<?php

namespace App\Services\Gamificacao\Enums;

enum Liga: string
{
    case Simonton = 'simonton';
    case Blackford = 'blackford';
    case Conceicao = 'conceicao';

    public function label(): string
    {
        return match ($this) {
            self::Simonton => 'Liga Simonton',
            self::Blackford => 'Liga Blackford',
            self::Conceicao => 'Liga Conceição (JMC)',
        };
    }
}
