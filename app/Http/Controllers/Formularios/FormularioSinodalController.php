<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormularioLocalRequest;
use App\Services\Formularios\FormularioSinodalService;
use Illuminate\Http\Request;
use Throwable;

class FormularioSinodalController extends Controller
{
    public function index()
    {
        $formulario_respondido_ano = FormularioSinodalService::getAnosFormulariosRespondidos();
        $formulario_esse_ano = FormularioSinodalService::getFormularioAnoCorrente();
        return view('dashboard.formularios.sinodal', [
            'coleta' => FormularioSinodalService::verificarColeta(),
            'anos' => $formulario_respondido_ano,
            'formulario' => $formulario_esse_ano
        ]);
    }

    public function store(StoreFormularioLocalRequest $request)
    {
        try {
            FormularioSinodalService::store($request);
            return redirect()->route('dashboard.formularios-sinodais.index')->with([
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

    public function view(Request $request)
    {
        try {
            $response = FormularioSinodalService::showFormulario($request->id);
            return response()->json(['data' => $response]);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()]);
        }
    }
    
    public function resumoTotalizador(Request $request)
    {
        try {
            $response = FormularioSinodalService::totalizador($request->id);
            return response()->json(['data' => $response]);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()]);
        }
    }

    public function validarImportacao(Request $request)
    {
        try {
            $response = FormularioSinodalService::validarImportacao($request);
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 400);
        }
    }

    public function importar(Request $request)
    {
        try {
            FormularioSinodalService::importar($request);
            return redirect()->route('dashboard.formularios-sinodais.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 400);
        }
    }

    public function getFederacoes()
    {
        try {
            return response()->json(FormularioSinodalService::getFederacoes());
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 400);
        }
    }

}
