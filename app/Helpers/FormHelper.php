<?php

namespace App\Helpers;

class FormHelper
{
    /**
     * Índice of SWITCH_ON
     * 
     * @var string
     */
    public const SWITCH_ON = 'on';
    
    /**
     * Índice number do Tipo do campo do FormBuildr.js
     * 
     * @var string
     */
    public const TIPO_NUMBER = 'numero';

    public static function statusFormatado(bool $status, string $ativo, string $inativo)
    {
        if ($status) {
            return  "<span class='badge badge-success'>{$ativo}</span>";
        }
        return  "<span class='badge badge-danger'>{$inativo}</span>";
    }

    public static function getUsarioInstancia($instancia, $campo)
    {
        $usuario = $instancia->usuario;
        if (!is_null($usuario)) {
            return $usuario->$campo;
        }
        return null;
    }

    public static function removerAcentos($string)
    {
        return preg_replace(
            [
                "/(á|à|ã|â|ä)/",
                "/(Á|À|Ã|Â|Ä)/",
                "/(é|è|ê|ë)/",
                "/(É|È|Ê|Ë)/",
                "/(í|ì|î|ï)/",
                "/(Í|Ì|Î|Ï)/",
                "/(ó|ò|õ|ô|ö)/",
                "/(Ó|Ò|Õ|Ô|Ö)/",
                "/(ú|ù|û|ü)/",
                "/(Ú|Ù|Û|Ü)/",
                "/(ñ)/",
                "/(Ñ)/",
                "/(ç)/",
                "/(Ç)/"
            ],
            explode(" ", "a A e E i I o O u U n N c C"), $string);
    }

    /**
     * Converte o valor no formato BRL X.XXX,XX para float XXXX.XX
     *
     * @param string $valor
     * @return float
     */
    public static function converterParaFloat(string $valor): float
    {
        $valorSemFormatacao = str_replace(',', '.', str_replace('.', '', $valor));
        return floatval($valorSemFormatacao);
    }

    /**
     * Converte o valor no formato de float XXXX.XX para X.XXX,XX
     *
     * @param string $valor
     * @return string
     */
    public static function converterParaReal(string $valor): string
    {
        return number_format($valor, 2, ',', '.');
    }

}
