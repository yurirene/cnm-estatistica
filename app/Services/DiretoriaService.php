<?php

namespace App\Services;

use App\Helpers\FormHelper;
use App\Models\Atividade;
use App\Models\Estado;
use App\Models\FormularioSinodal;
use App\Models\Sinodal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DiretoriaService
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
        return round(($valor * 100) / $total, 2);
    }

    public static function getFormularioEntregue() : array
    {
        $retorno = [];
        $sinodais = Sinodal::whereIn('regiao_id', Auth::user()->regioes->pluck('id'))->get();
        foreach ($sinodais as $sinodal) {
            $status = true;
            $formulario = FormularioSinodal::where('ano_referencia', date('Y'))
                ->where('sinodal_id', $sinodal->id)
                ->first();
            if (!$formulario) {
                $status = false;
            }
            $retorno[] = [
                'sinodal' => $sinodal->nome,
                'status' => FormHelper::statusFormatado($status, 'Entregue', 'Pendente')
            ];
        }
        return $retorno;
    }
}