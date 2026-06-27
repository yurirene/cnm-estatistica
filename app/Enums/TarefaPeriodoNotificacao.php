<?php

namespace App\Enums;

enum TarefaPeriodoNotificacao: string
{
    case Diario = 'diario';
    case ACada2Dias = 'a_cada_2_dias';
    case ACada3Dias = 'a_cada_3_dias';
    case Semanal = 'semanal';
    case Quinzenal = 'quinzenal';
    case Mensal = 'mensal';

    public function dias(): int
    {
        return match ($this) {
            self::Diario => 1,
            self::ACada2Dias => 2,
            self::ACada3Dias => 3,
            self::Semanal => 7,
            self::Quinzenal => 15,
            self::Mensal => 30,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Diario => 'Diário',
            self::ACada2Dias => 'A cada 2 dias',
            self::ACada3Dias => 'A cada 3 dias',
            self::Semanal => 'Semanal',
            self::Quinzenal => 'Quinzenal',
            self::Mensal => 'Mensal',
        };
    }
}
