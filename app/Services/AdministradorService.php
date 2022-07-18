<?php

namespace App\Services;

use App\Models\Estado;
use Illuminate\Support\Facades\Auth;

class AdministradorService
{
    public static function getTotalizadores()
    {
        
    }

    public static function getMapa()
    {

    }


    public static function getDefaultMap()
    {
        $estados = Estado::get()
            ->map(function($item) {
                return 'br-' . strtolower($item->sigla);
            });


        $data = array();

        foreach ($estados as $estado) {
            $quantidade_socios = self::getTotalFederacoesResponderam($estado);
            $data[] = [$estado, $quantidade_socios];
        }
        return $data;
    }

    public static function getTotalFederacoesResponderam($estado) {
        //
    }



}