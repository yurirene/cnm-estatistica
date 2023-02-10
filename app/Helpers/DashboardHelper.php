<?php

namespace App\Helpers;

use App\Models\Parametro;
use App\Services\AdministradorService;
use App\Services\Instancias\DiretoriaService;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Instancias\FederacaoService;
use App\Services\Instancias\LocalService;
use App\Services\Instancias\SinodalService;
use App\Services\Produtos\ProdutoService;

class DashboardHelper
{

    public static function make()
    {

        if (auth()->user()->hasRole(['sinodal'])) {
            return app()->make(SinodalService::class);
        } else if (auth()->user()->hasRole(['federacao'])) {
            return app()->make(FederacaoService::class);
        } else if (auth()->user()->hasRole(['diretoria'])) {
            return app()->make(DiretoriaService::class);
        } else if (auth()->user()->hasRole(['administrador'])) {
            return app()->make(AdministradorService::class);
        } else if (auth()->user()->hasRole(['local'])) {
            return app()->make(LocalService::class);
        } else if (auth()->user()->hasRole(['secretaria_estatistica'])) {
            return app()->make(EstatisticaService::class);
        } else if (auth()->user()->hasRole(['secreatria_produtos'])) {
            return app()->make(ProdutoService::class);
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


    public static function entregouRelatorio(): bool
    {
        $instancia = auth()->user()->instancia() ? auth()->user()->instancia()->first() : null;
        if (!$instancia) {
            return true;
        }
        $ano = Parametro::where('nome', 'ano_referencia')->first()->valor;
        return $instancia->relatorios()->where('ano_referencia', $ano)->get()->isNotEmpty();
    }


    public static function getAvisosUsuario(): array
    {
        return auth()->user()
            ->avisos()
            ->where('ativo', true)
            ->select(['titulo', 'texto'])
            ->get()
            ->toArray();
    }

    public static function getAvisosUsuarioModal(): array
    {
        $aviso = auth()->user()
            ->avisos()
            ->where('ativo', true)
            ->where('modal', true)
            ->wherePivot('visualizado', false)
            ->select(['avisos.id', 'titulo', 'texto'])
            ->first();
        if (is_null($aviso)) {
            return [];
        }
        return $aviso->toArray();
    }

    public static function getQualidadeEntregaRelatorios(): array
    {
        return DiretoriaService::getQualidadeEntregaRelatorios();
    }

}
