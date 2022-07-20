<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\RegistroLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdministradorService
{
    public static function getTotalizadores()
    {
        return [
            'grafico_acesso_trinta_dias' => self::getAcessosTrintaDias()
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
            $quantidade_socios = self::getTotalFederacoesResponderam($estado);
            $data[] = [$estado, $quantidade_socios];
        }
        return $data;
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

    public static function getTotalFederacoesResponderam($estado) {
        //
    }



}