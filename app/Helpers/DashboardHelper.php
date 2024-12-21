<?php

namespace App\Helpers;

use App\Models\ComprovanteACI;
use App\Models\Parametro;
use App\Services\AdministradorService;
use App\Services\Instancias\DiretoriaNacionalService;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Instancias\FederacaoService;
use App\Services\Instancias\LocalService;
use App\Services\Instancias\SinodalService;
use App\Services\Produtos\ProdutoService;
use Illuminate\Support\Facades\Gate;

class DashboardHelper
{

    public static function make()
    {
        $service = null;

        if (Gate::check(['sinodal'])) {
            $service = app()->make(SinodalService::class);
        } elseif (Gate::check(['federacao'])) {
            $service = app()->make(FederacaoService::class);
        } elseif (Gate::check(['diretoria'])) {
            $service = app()->make(DiretoriaNacionalService::class);
        } elseif (Gate::check(['isAdmin'])) {
            $service = app()->make(AdministradorService::class);
        } elseif (Gate::check(['local'])) {
            $service = app()->make(LocalService::class);
        } elseif (Gate::check(['secretaria_estatistica'])) {
            $service = app()->make(EstatisticaService::class);
        } elseif (Gate::check(['secreatria_produtos'])) {
            $service = app()->make(ProdutoService::class);
        }

        return $service;
    }

    public static function getTotalizadores()
    {
        $class = self::make();
        
        if (is_null($class)) {
            return [];
        }
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
        $ano = EstatisticaService::getAnoReferencia();
        return $instancia->relatorios()->where('ano_referencia', $ano)->get()->isNotEmpty();
    }

    /**
     * Verifica se anexou o comprovante de ACI
     * usado em avisos
     *
     * @return boolean
     */
    public static function entregouComprovante(): bool
    {
        $instancia = auth()->user()->instancia() ? auth()->user()->instancia()->first() : null;
        if (!$instancia) {
            return true;
        }
        $anoReferencia = EstatisticaService::getAnoReferencia();
        return ComprovanteACI::where('sinodal_id', $instancia->id)
            ->where('ano', $anoReferencia)
            ->count();
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
        return DiretoriaNacionalService::getQualidadeEntregaRelatorios();
    }

}
