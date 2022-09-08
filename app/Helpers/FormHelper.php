<?php

namespace App\Helpers;

class FormHelper
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

    public static function removerAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/", "/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $string);
    }

}
