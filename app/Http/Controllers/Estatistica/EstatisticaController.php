<?php

namespace App\Http\Controllers\Estatistica;

use App\Http\Controllers\Controller;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Estatistica\GraficoEstatisticaService;
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

    public function atualizarRanking()
    {
        try {
            EstatisticaService::atualizarRanking();
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function graficos(Request $request)
    {
        return response()->json(GraficoEstatisticaService::graficos($request->all()), 200);
    }

    public function externo()
    {
        return view('dashboard.index.estatistica.index', [
            'externo' => true
        ]);
    }
}
