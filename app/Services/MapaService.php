<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\Local;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Instancias\DashboardExecutivoService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class MapaService
{

    public static function getEstadosUsuario()
    {
        $estados = Estado::when(!Gate::check(['presidente']), function ($query) {
                return $query->where('regiao_id', auth()->user()->regiao_id);
            })
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
                'n_federacoes' => $totalizador['n_federacoes'],
                'taxa_resposta' => $totalizador['taxa_resposta'] ?? 0,
                'aci' => $totalizador['aci'] ?? 0,
            ];
        }
        return $data;
    }

    public static function getTotalizador(string $estado, ?string $filtroAno = null) : array
    {
        try {
            $sigla = explode('-', $estado);
            $estadoModel = Estado::where('sigla', strtoupper($sigla[1] ?? ''))->first();
            if (!$estadoModel) {
                return [
                    'n_socios' => 0,
                    'n_umps' => 0,
                    'n_federacoes' => 0,
                    'taxa_resposta' => 0,
                    'aci' => 0,
                ];
            }
            $ano = $filtroAno ?: EstatisticaService::getAnoReferencia();
            $formularios = FormularioLocal::whereHas('local', function ($sql) use ($estadoModel) {
                return $sql->where('estado_id', $estadoModel->id);
            })
                ->where('ano_referencia', $ano)
                ->get();

            $total = 0;
            foreach ($formularios as $formulario) {
                $total += (intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores']));
            }

            $umpsAtivas = Local::where('estado_id', $estadoModel->id)->where('status', 1)->count();
            $umpsEntregues = FormularioLocal::whereHas('local', function ($sql) use ($estadoModel) {
                    return $sql->where('estado_id', $estadoModel->id)->where('status', 1);
                })
                ->where('ano_referencia', $ano)
                ->count();
            $taxaResposta = $umpsAtivas > 0
                ? round(($umpsEntregues * 100) / $umpsAtivas, 1)
                : 0;

            $aci = 0.0;
            $federacaoIds = Federacao::where('estado_id', $estadoModel->id)->where('status', 1)->pluck('id');
            $formulariosFederacao = FormularioFederacao::whereIn('federacao_id', $federacaoIds)
                ->where('ano_referencia', $ano)
                ->get();
            foreach ($formulariosFederacao as $formularioFederacao) {
                $aci += DashboardExecutivoService::parseValorAci(
                    $formularioFederacao->aci['valor'] ?? $formularioFederacao->aci['valor_repassado'] ?? 0
                );
            }

            return [
                'n_socios' => $total,
                'n_umps' => $umpsAtivas,
                'n_federacoes' => Federacao::where('estado_id', $estadoModel->id)->where('status', 1)->count(),
                'taxa_resposta' => $taxaResposta,
                'aci' => round($aci, 2),
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
