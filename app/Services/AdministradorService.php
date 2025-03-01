<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Parametro;
use App\Models\Regiao;
use App\Models\Sinodal;
use App\Services\Estatistica\EstatisticaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdministradorService
{
    public static function getTotalizadores()
    {
        return [
            'total_relatorios_entregues' => self::getTotalRelatoriosEntregues(),
            'total_relatorios_pendentes' => self::getTotalRelatoriosPendentes(),
            'grafico_entrega_formulario_por_regiao' => self::getGraficoEntregaRelatorioPorRegiao(),
        ];

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
            $quantidade_socios =0;
            $data[] = [$estado, $quantidade_socios];
        }
        return $data;
    }

    public static function getTotalRelatoriosPendentes()
    {
        return Sinodal::whereDoesntHave('relatorios', function($sql) {
                return $sql->where('ano_referencia', EstatisticaService::getAnoReferencia())
                    ->whereIn('status', [0,1]);
            })
            ->where('status', 1)
            ->get()
            ->count();

        }
    public static function getTotalRelatoriosEntregues()
    {
        return Sinodal::whereHas('relatorios', function($sql) {
                return $sql->where('ano_referencia', EstatisticaService::getAnoReferencia())
                    ->whereIn('status', [0,1]);
            })
            ->get()
            ->count();
    }

    public static function getDataFormatadaGrafico(array $dados)
    {
        $retorno = [];
        foreach ($dados as $label => $dado) {
            $retorno[] = Carbon::parse($label)->format('d/m');
        }
        return array_values($retorno);
    }

    public static function getGraficoEntregaRelatorioPorRegiao()
    {

        try {
            $regioes = Regiao::get();
            $total_regiao = [
                'labels' => ['Vigília e Oração', 'Social', 'Evangelistico/Missional', 'Espiritual', 'Recreativo' ],
                'datasets' => [
                    [
                        'data' => [],
                        'backgroundColor' => ['#003f5c','#58508d','#bc5090','#ff6361','#ffa600']
                    ]
                ]
            ];
            foreach ($regioes as $key => $regiao) {
                $total_regiao['labels'][$key] = $regiao->nome;
                $total_regiao['datasets'][0]['data'][$key] = self::porcentagemEntregue($regiao);
            }
            return $total_regiao;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function porcentagemEntregue($regiao)
    {
        try {
            $total_sinodais = Sinodal::where('regiao_id', $regiao->id)->get()->count();
            $total_entregue = Sinodal::where('regiao_id', $regiao->id)->whereHas('relatorios', function($sql) {
                    return $sql->where('ano_referencia', EstatisticaService::getAnoReferencia());
                })
                ->get()
                ->count();
            $total = ($total_entregue * 100) / $total_sinodais;
            return floatval(number_format($total, 2));
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }



}
