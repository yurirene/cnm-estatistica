<?php

namespace App\Services\Formularios;

class CamposFormularioService
{
    public const DISCIPULADO = [
        'trilha_cnm',
        'discipulando_cnm',
        'discipulando_outro',
        'sendo_discipulados',
    ];

    public static function discipuladoZerado(): array
    {
        return array_fill_keys(self::DISCIPULADO, 0);
    }

    public static function somarDiscipulado(array $acumulado, $formulario): array
    {
        foreach (self::DISCIPULADO as $campo) {
            if (!isset($acumulado[$campo])) {
                $acumulado[$campo] = 0;
            }
            $acumulado[$campo] += intval(data_get($formulario, "discipulado.{$campo}", 0));
        }

        return $acumulado;
    }

    public static function mapearInteiros(?array $dados): array
    {
        return array_map('intval', $dados ?? []);
    }
}
