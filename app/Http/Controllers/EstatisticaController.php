<?php

namespace App\Http\Controllers;

use App\Services\EstatisticaService;
use Illuminate\Http\Request;
use Throwable;

class EstatisticaController extends Controller
{

    public function index()
    {
        $parametros = EstatisticaService::getParametros();
        $anos_referencias = EstatisticaService::getAnoReferenciaFormularios();
        return view('dashboard.estatistica.index',[
            'parametros' => $parametros,
            'anos_referencias' => $anos_referencias
        ]);
    }

    public function exportarExcel(Request $request)
    {
        return EstatisticaService::exportarExcel($request->all());
    }

    public function atualizarParametro(Request $request)
    {
        try {
            EstatisticaService::atualizarParametro($request->all());
            return response()->json(['mensagem' => 'Parâmetro Atualizado'], 200);
        } catch (Throwable $th) {
            return response()->json(['mensagem' => 'Erro ao Atualizar Parâmetro'], 500);
        }
    }
}