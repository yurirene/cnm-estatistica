<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormularioLocalRequest;
use App\Models\Parametro;
use App\Services\ColetorDadosService;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\FormularioLocalService;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class FormularioLocalController extends Controller
{
    public function index()
    {
        $formularioRespondidoAno = FormularioLocalService::getAnosFormulariosRespondidos();
        $formularioColetaAtual = FormularioLocalService::getFormularioAnoCorrente();
        return view('dashboard.formularios.local', [
            'coleta' => FormularioLocalService::verificarColeta(),
            'anos' => $formularioRespondidoAno,
            'ano_referencia' => EstatisticaService::getAnoReferencia(),
            'formulario' => $formularioColetaAtual,
            'coletorDados' => ColetorDadosService::carregarDadosCompilados()
        ]);
    }

    public function store(StoreFormularioLocalRequest $request)
    {
        try {
            FormularioLocalService::store($request);
            return redirect()->route('dashboard.formularios-locais.index')->with([
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
            $response = FormularioLocalService::showFormulario($request->id);
            return response()->json(['data' => $response]);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()]);
        }
    }


    public function export($ano)
    {
        try {
            $formulario = FormularioLocalService::getFormulario($ano);
            if (!$formulario) {
                return redirect()->route('dashboard.formularios-locais.index')->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => 'Preencha o formulário primeiro!'
                    ]
                ]);
            }

            return view('dashboard.formularios.local.export', [
                'formulario' => FormularioLocalService::getFormulario($ano)
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.formularios-locais.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function localExport($local)
    {
        $formulario = FormularioLocalService::getFormularioLocal($local);

        if (!$formulario) {
            throw new Exception("Formulário não existe", 500);
            
        }
        return view('dashboard.formularios.local.export', [
            'formulario' => $formulario
        ]);
    }
}
