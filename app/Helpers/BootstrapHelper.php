<?php

namespace App\Helpers;

class BootstrapHelper
{

    public static function badge(string $cor, string $texto, bool $pill = false)
    {
        return  '<span class="badge ' . ($pill ? "badge-pill" : '') . ' badge-' . $cor . '">' . $texto .'</span>';
    }

}