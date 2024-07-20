<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Models\Parametro;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\FormularioFederacaoService;
use Illuminate\Http\Request;
use Throwable;

class FormularioFederacaoController extends Controller
{
    public function index()
    {
        $listaAnosFormulariosRespondidos = FormularioFederacaoService::getAnosFormulariosRespondidos();
        $formularioDesseAno = FormularioFederacaoService::getFormularioAnoCorrente();
        return view('dashboard.formularios.federacao', [
            'coleta' => FormularioFederacaoService::verificarColeta(),
            'anos' => $listaAnosFormulariosRespondidos,
            'ano_referencia' => EstatisticaService::getAnoReferencia(),
            'formularioEntregue' => isset($formularioDesseAno)
                && $formularioDesseAno->status == EstatisticaService::FORMULARIO_ENTREGUE,
            'qualidade_entrega' =>  FormularioFederacaoService::qualidadeEntrega(),
            'estrutura_federacao' => FormularioFederacaoService::getEstrutura(),
            'formulario' => $formularioDesseAno
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


    public function salvarPreenchimento(Request $request)
    {
        try {
            FormularioFederacaoService::store($request, true);
            return redirect()->route('dashboard.formularios-federacoes.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Formulário Salvo com sucesso!'
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


    public function export($ano)
    {
        return view('dashboard.formularios.federacao.export', [
            'formulario' => FormularioFederacaoService::getFormulario($ano)
        ]);
    }

    public function federacaoExport($federacao)
    {
        return view('dashboard.formularios.federacao.export', [
            'formulario' => FormularioFederacaoService::getFormularioDaFederacao($federacao)
        ]);
    }
}
