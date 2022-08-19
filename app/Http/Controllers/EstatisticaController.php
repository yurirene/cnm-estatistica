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
        return view('dashboard.estatistica.index',[
            'parametros' => $parametros
        ]);
    }

    public function atualizarParametro(Request $request)
    {
        try {
            EstatisticaService::atualizarParametro($request->all());
            return redirect()->route('dashboard.estatistica.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
                ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }
}
