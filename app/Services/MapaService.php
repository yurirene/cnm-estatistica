<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MapaService
{

    public static function getEstadosUsuario()
    {
        $usuario = User::find(Auth::id());

        $estados = Estado::whereIn('regiao_id', $usuario->regioes->pluck('id'))
            ->get()
            ->map(function($item) {
                return 'br-' . strtolower($item->sigla);
            });
        return $estados;
    }

    public static function getDefaultMap()
    {
        $estados = self::getEstadosUsuario();
        $data = array();

        foreach ($estados as $estado) {
            $data[] = [$estado, 0];
        }
        return $data;
    }
}