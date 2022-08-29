<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Regiao;
use App\Models\RegistroLogin;
use App\Models\Sinodal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdministradorService
{
    public static function getTotalizadores()
    {
        return [
            'total_acessos_hoje' => self::getTotalAcessosHoje(),
            'total_relatorios_entregues' => self::getTotalRelatoriosEntregues(),
            'total_relatorios_pendentes' => self::getTotalRelatoriosPendentes(),
            'total_acessos_trinta_dias' => self::getTotalAcessosTrintaDias(),
            'grafico_acesso_trinta_dias' => self::getAcessosTrintaDias(),
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
                return $sql->where('ano_referencia', date('Y'));
            })
            ->where('status', 1)
            ->get()
            ->count();
    
        }
    public static function getTotalRelatoriosEntregues()
    {
        return Sinodal::whereHas('relatorios', function($sql) {
                return $sql->where('ano_referencia', date('Y'));
            })
            ->get()
            ->count();
    }

    public static function getTotalAcessosHoje()
    {
        try {
            $hoje = Carbon::now()->format('Y-m-d');
            $total = RegistroLogin::whereDate('created_at', $hoje)
                ->get()
                ->count();
            return $total;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }
    }
    public static function getTotalAcessosTrintaDias()
    {
        try {
            $hoje = Carbon::now()->format('Y-m-d');
            $trinta_dias_atras = Carbon::now()->subMonth()->format('Y-m-d');
            $total = RegistroLogin::whereBetween('created_at', [$trinta_dias_atras, $hoje])
                ->get()
                ->count();
            return $total;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }
    }

    public static function getAcessosTrintaDias()
    {
        try {
            $hoje = Carbon::now()->format('Y-m-d');
            $trinta_dias_atras = Carbon::now()->subMonth()->format('Y-m-d');

            $logins = RegistroLogin::select(DB::raw('DATE(created_at) as dia'), DB::raw('count(*) as quantidade'))
                ->whereBetween('created_at', [$trinta_dias_atras, $hoje])
                ->groupBy('dia')
                ->get()
                ->pluck('quantidade', 'dia');

            return [
                'labels' => self::getDataFormatadaGrafico($logins->toArray()),
                'datasets' => [
                    [
                        'label' => 'Quantidade de Acessos',
                        'data' => $logins->values()->toArray(),
                        'borderColor' => '#ffa600',
                        'backgroundColor' => '#ff6361',
                        'tension' => 0.4
                    ],
                ]
            ];            
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }


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
                    return $sql->where('ano_referencia', date('Y'));
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