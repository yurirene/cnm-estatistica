<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class SiteHelper
{

    public static function galeria($sinodalId, $campo)
    {
        $fotos = [1,2,3,4,5,6,7,8,9,10,11,12];
        return view('dashboard.apps.sites.custom.galeria', [
            'fotos' => $fotos,
            'sinodal_id' => $sinodalId
        ]);
    }

    public static function diretoria($sinodalId, $campo)
    {
        return view('dashboard.apps.sites.custom.diretoria', [
            'campos' => $campo,
            'sinodal_id' => $sinodalId
        ]);
    }

}
