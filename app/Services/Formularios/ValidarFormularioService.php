<?php

namespace App\Services\Formularios;

class ValidarFormularioService
{
    public static function somatorio(int $total, ...$campos) : bool
    {
        $somatorio = 0;
        foreach ($campos as $valor) {
            $somatorio += intval($valor);
        }
        return $somatorio == $total;
    }

    public static function limite(int $total, $valor) : bool 
    {
        return intval($valor) <= $total;
    }
}