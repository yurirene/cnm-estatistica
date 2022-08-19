<?php

namespace App\Services;

use App\Models\Parametro;
use Illuminate\Support\Facades\Auth;

class EstatisticaService
{

    public const PARAMETROS = [
        'coleta_dados'
    ];

    public static function atualizarParametro(array $request)
    {
        try {
            dd($request);
            $parametro = Parametro::where('nome', 'coleta_dados')->first();
            $parametro->update([
                'valor' => $request['coleta'] ? 'SIM' : 'NAO'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getParametros()
    {
        try {
            return Parametro::whereIn('nome', self::PARAMETROS)->get()->map(function($item) {
                return [
                    'nome' => $item->nome,
                    'valor' => $item->valor,
                    'label' => $item->descricao
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}