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

}