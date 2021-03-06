<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Models\Parametro;
use App\Services\Formularios\FormularioFederacaoService;
use Illuminate\Http\Request;
use Throwable;

class FormularioFederacaoController extends Controller
{
    public function index()
    {
        $formulario_respondido_ano = FormularioFederacaoService::getAnosFormulariosRespondidos();
        return view('dashboard.formularios.federacao', [
            'coleta' => FormularioFederacaoService::verificarColeta(),
            'anos' => $formulario_respondido_ano
        ]);
    }

    public function store(Request $request)
    {
        try {
            FormularioFederacaoService::store($request);
            return redirect()->route('dashboard.formularios-federacoes.index')->with([
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
            $response = FormularioFederacaoService::showFormulario($request->id);
            return response()->json(['data' => $response]);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()]);
        }
    }
    
    public function resumoTotalizador(Request $request)
    {
        try {
            $response = FormularioFederacaoService::totalizador($request->id);
            return response()->json(['data' => $response]);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()]);
        }
    }
}
