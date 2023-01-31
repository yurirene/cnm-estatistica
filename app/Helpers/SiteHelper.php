<?php

namespace App\Helpers;

use App\Models\Apps\Site\Galeria;
use Illuminate\Support\Facades\Auth;

class SiteHelper
{

    public static function galeria($sinodal_id, $campo, $chave)
    {
        $fotos = Galeria::where('sinodal_id', $sinodal_id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'path' => $item->path
                ];
            })
            ->toArray();
        return view('dashboard.apps.sites.custom.galeria', [
            'fotos' => $fotos,
            'sinodal_id' => $sinodal_id
        ]);
    }

    public static function diretoria($sinodalId, $campo, $chave)
    {
        return view('dashboard.apps.sites.custom.diretoria', [
            'chave' => $chave,
            'campos' => $campo,
            'sinodal_id' => $sinodalId
        ]);
    }

}
