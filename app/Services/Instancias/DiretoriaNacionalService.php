<?php

namespace App\Services\Instancias;

use App\Helpers\FormHelper;
use App\Models\Atividade;
use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
use Illuminate\Support\Facades\Auth;

class DiretoriaNacionalService
{

    public static function getGraficoAtividades() : array
    {
        $usuario = Auth::id();
        $total_programacoes = Atividade::where('user_id', $usuario)
            ->where('start', '>=', date('Y').'-01-01')
            ->where('status', 1)
            ->count();
        $retorno = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Tipo de Atividades',
                    'data' => [],
                    'borderColor' => '#ccc',
                    'backgroundColor' => '#ffa600'
                ]
            ]
        ];
        foreach (Atividade::TIPOS as $tipo => $texto) {
            $quantidade = Atividade::where('tipo', $tipo)
                ->where('user_id', $usuario)
                ->where('start', '>=', date('Y').'-01-01')
                ->where('status', 1)
                ->count();
            $retorno['labels'][] = $texto;
            $retorno['datasets'][0]['data'][] =  self::porcentagem($total_programacoes, $quantidade);
        }
        return $retorno;
    }

    public static function porcentagem(int $total, int $valor) : float
    {
        if ($total == 0) {
            return  0;
        }
        return round(($valor * 100) / $total, 2);
    }

    public static function getFormularioEntregue() : array
    {
        $retorno = [];
        $sinodais = Sinodal::whereIn('regiao_id', Auth::user()->regioes->pluck('id'))->get();
        foreach ($sinodais as $sinodal) {
            $status = true;
            $formulario = FormularioSinodal::where('ano_referencia', EstatisticaService::getAnoReferencia())
                ->where('sinodal_id', $sinodal->id)
                ->first();
            if (!$formulario) {
                $status = false;
            }
            $retorno[] = [
                'id' => $sinodal->id,
                'sinodal' => $sinodal->nome,
                'status' => FormHelper::statusFormatado($status, 'Entregue', 'Pendente')
            ];
        }
        return $retorno;
    }


    public static function getTotalizadores()
    {
        try {
            $sinodais = Sinodal::whereIn('regiao_id', Auth::user()->regioes->pluck('id'))->get();
            $federacoes = Federacao::whereIn('sinodal_id', $sinodais->pluck('id'))->get();
            $umps = Local::whereIn('federacao_id', $federacoes->pluck('id'))->get();
            $formularios = FormularioLocal::whereHas('local', function ($sql) use ($sinodais) {
                $sql->whereIn('sinodal_id', $sinodais->pluck('id'));
            })
            ->where('ano_referencia', EstatisticaService::getAnoReferencia())
            ->get();

            $totalSocios = 0;
            $totalUmps = $umps->where('status', '=', 1)->count();
            foreach ($formularios as $formulario) {
                $totalSocios += intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores']);
            }
            return [
                'total_sinodos' => $sinodais->count(),
                'total_presbiterios' => $federacoes->count(),
                'total_igrejas' => $umps->count(),
                'total_n_sociedades_internas' => $umps->where('outro_modelo', true)->count(),
                'total_sinodais' => $sinodais->where('status', true)->count(),
                'total_federacoes' => $federacoes->where('status', true)->count(),
                'total_umps' => $totalUmps,
                'total_socios' => $totalSocios
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getQualidadeEntregaRelatorios()
    {
        try {
            $sinodais = Sinodal::whereIn('regiao_id', auth()->user()->regioes->pluck('id'))->get();
            $quantidadeUmps = Local::whereIn('sinodal_id', $sinodais->pluck('id'))
                ->where('status', true)
                ->whereHas('federacao', function ($q) {
                    return $q->where('status', true);
                })
                ->count();

            $quantidadeFormularios = FormularioLocal::whereHas('local', function ($sql) use ($sinodais) {
                    $sql->whereIn('sinodal_id', $sinodais->pluck('id'))
                        ->where('status', true)
                        ->whereHas('federacao', function ($q) {
                            return $q->where('status', true);
                        });
                })
                ->where('ano_referencia', EstatisticaService::getAnoReferencia())
                ->count();

            $restante = $quantidadeUmps - $quantidadeFormularios;

            return [
                "labels" => ['Entregue', 'Pendente'],
                "datasets" => [
                    [
                        "label" => 'Formulários',
                        "data" => [$quantidadeFormularios, $restante],
                        "backgroundColor" => ["#ffa322", "#22054e"],
                        'borderColor' => "#ffa9001f"
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
