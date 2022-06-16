<?php

namespace App\Helpers;

use App\Services\DiretoriaService;
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
        } else if (auth()->user()->hasRole(['diretoria'])) {
            return app()->make(DiretoriaService::class);
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

    public static function getGraficoAtividades() : array
    {
        $class = self::make();
        return $class::getGraficoAtividades();
    }

    public static function getFormularioEntregue() : array
    {
        $class = self::make();
        return $class::getFormularioEntregue();
    }
    
    
    
}