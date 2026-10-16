<?php

namespace App\Services\Gamificacao\Guards;

class EscritorPlacar
{
    private static int $nivel = 0;

    public static function executar(callable $fn): mixed
    {
        self::$nivel++;

        try {
            return $fn();
        } finally {
            self::$nivel--;
        }
    }

    public static function liberado(): bool
    {
        return self::$nivel > 0;
    }
}
