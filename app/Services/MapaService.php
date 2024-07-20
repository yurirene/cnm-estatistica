<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
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
        if (Auth::user()->admin) {
            return AdministradorService::getMapa();
        }
        $estados = self::getEstadosUsuario();
        $data = [];

        foreach ($estados as $estado) {
            $totalizador = self::getTotalizador($estado);
            $data[] = [
                'hc-key' => $estado,
                'n_socios' => $totalizador['n_socios'],
                'n_umps' => $totalizador['n_umps'],
                'n_federacoes' => $totalizador['n_federacoes']
            ];
        }
        return $data;
    }

    public static function getTotalizador(string $estado, string $filtroAno = null) : array
    {
        try {
            $sigla = explode('-', $estado);
            $estado = Estado::where('sigla', strtoupper($sigla[1]))->first();
            $ano = EstatisticaService::getAnoReferencia();
            $formularios = FormularioLocal::whereHas('local', function ($sql) use ($estado) {
                return $sql->where('estado_id', $estado->id);
            })
                ->when(!is_null($filtroAno), function ($sql) use ($filtroAno) {
                    return $sql->where('ano_referencia', $filtroAno);
                }, function ($sql) use ($ano) {
                    return $sql->where('ano_referencia', $ano);
                })
                ->get();
            $total = 0;
            foreach ($formularios as $formulario) {
                $total += (intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores']));
            }
            return [
                'n_socios' => $total,
                'n_umps' => Local::where('estado_id', $estado->id)->where('status', 1)->count(),
                'n_federacoes' => Federacao::where('estado_id', $estado->id)->where('status', 1)->count()
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
