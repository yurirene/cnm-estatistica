<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
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
        if (Auth::user()->admin) {
            return AdministradorService::getMapa();
        }
        $estados = self::getEstadosUsuario();
        $data = array();

        foreach ($estados as $estado) {
            $quantidade_socios = self::getTotalSocios($estado);
            $data[] = [$estado, $quantidade_socios];
        }
        return $data;
    }

    public static function getTotalSocios(string $estado) : int
    {
        try {
            $sigla = explode('-',$estado);
            $estado = Estado::where('sigla', strtoupper($sigla[1]))->first();
            $federacoes = Federacao::where('estado_id', $estado->id)->get();
            $formularios = FormularioFederacao::whereIn('federacao_id', $federacoes->pluck('id'))->get();
            $total = 0;
            foreach ($formularios as $formulario) {
                $total += (intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores']));
            }
            return $total;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}