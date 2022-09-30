<?php

namespace App\Helpers;

class BootstrapHelper
{

    public static function badge(string $cor, string $texto, bool $pill = false)
    {
        return  '<span class="badge ' . ($pill ? "badge-pill" : '') . ' badge-' . $cor . '">' . $texto .'</span>';
    }

    public static function buttonModal(string $cor, string $texto, string $idModal)
    {
        return  `<button type="button" class="btn btn-{$cor}" data-toggle="modal" data-target="#{$idModal}">
                    {$texto}
                </button>`;
    }

    public static function buttonLink(string $cor, string $texto, string $link, bool $new = false)
    {
        return  '<a href="' . $link . '"' . ($new ? 'target="_blank"' : '') . ' class="btn btn-sm btn-' . $cor . '"> ' . $texto . ' </a>';
    }

}