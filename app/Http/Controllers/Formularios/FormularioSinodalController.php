<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\FormularioSinodalService;
use Illuminate\Http\Request;
use Throwable;

class FormularioSinodalController extends Controller
{
    public function index()
    {
        $formularioRespondidoAno = FormularioSinodalService::getAnosFormulariosRespondidos();
        $formularioDesseAno = FormularioSinodalService::getFormularioAnoCorrente();
        return view('dashboard.formularios.sinodal', [
            'coleta' => FormularioSinodalService::verificarColeta(),
            'anos' => $formularioRespondidoAno,
            'formulario' => $formularioDesseAno,
            'ano_referencia' => EstatisticaService::getAnoReferencia(),
            'qualidade_entrega' =>  FormularioSinodalService::qualidadeEntrega(),
            'estrutura_sinodal' => FormularioSinodalService::getEstrutura(),
            'formularioEntregue' => isset($formularioDesseAno)
                && $formularioDesseAno->status == EstatisticaService::FORMULARIO_ENTREGUE
        ]);
    }

    public function store(Request $request)
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


    public function salvarPreenchimento(Request $request)
    {
        try {
            FormularioSinodalService::store($request, true);
            return redirect()->route('dashboard.formularios-sinodais.index')->with([
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


    public function getFederacoes()
    {
        try {
            return response()->json(FormularioSinodalService::getFederacoes());
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 400);
        }
    }

    public function export($ano)
    {
        try {
            $formulario = FormularioSinodalService::getFormulario($ano);
            if (!$formulario) {
                return redirect()->route('dashboard.formularios-sinodal.index')->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => 'Preencha o formulário primeiro!'
                    ]
                ]);
            }

            return view('dashboard.formularios.sinodal.export', [
                'formulario' => FormularioSinodalService::getFormulario($ano)
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.formularios-sinodal.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function SinodalExport($sinodal)
    {
        return view('dashboard.formularios.sinodal.export', [
            'formulario' => FormularioSinodalService::getFormularioDaSinodal($sinodal)
        ]);
    }

}
