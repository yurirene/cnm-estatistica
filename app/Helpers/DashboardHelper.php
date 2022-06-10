<?php

namespace App\Helpers;

use App\Services\FederacaoService;
use App\Services\SinodalService;

class DashboardHelper
{
    
    public static function make()
    {
        if (auth()->user()->hasRole(['administrador', 'sinodal'])) {
            return app()->make(SinodalService::class);
        } else if (auth()->user()->hasRole(['federacao'])) {
            return app()->make(FederacaoService::class);
        }
    }
    
    public static function getTotalizadores()
    {
        $class = self::make();
        return $class::getTotalizadores();
    }

    public static function getInfo()
    {

        $class = self::make();
        return $class::getInfo();
    }
    
    public static function getTotalLocais()
    {
        return 10;
    }
    
    
    
}