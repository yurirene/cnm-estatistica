<?php

namespace App\Helpers;

class AuthHelper
{

    public static function statusFormatado(bool $status, string $ativo, string $inativo)
    {
        if ($status) {
            return  '<span class="badge badge-success">' . $ativo .'</span>';
        }
        return  '<span class="badge badge-danger">' . $inativo .'</span>';
    }

    public static function getUsarioInstancia($instancia, $campo)
    {
        $usuario = $instancia->usuario->first();
        if (!is_null($usuario)) {
            return $usuario->$campo;
        }
        return null;
    }

}