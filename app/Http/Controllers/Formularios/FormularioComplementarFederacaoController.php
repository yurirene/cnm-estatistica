<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Services\Formularios\FormularioComplementarService;
use Illuminate\Http\Request;
use Throwable;

class FormularioComplementarFederacaoController extends Controller
{
    public function index()
    {
        $ano = request()->filled('ano') ? request()->get('ano') : date('Y');

        return view('dashboard.formulario-complementar.form', [
            'formulario' => FormularioComplementarService::getFormularioComplementar(
                auth()->user()->federacao_id,
                FormularioComplementarService::TIPO_FORMULARIO_FEDERACAO,
                $ano
            ),
            'anos' => FormularioComplementarService::getAnosToSelect(),
            'route' => 'formulario-complementar-federacao',
            'respostas' => FormularioComplementarService::getRespostas(
                auth()->user()->federacao_id,
                FormularioComplementarService::TIPO_FORMULARIO_FEDERACAO,
                $ano
            )
        ]);
    }

    public function update(string $formulario, Request $request)
    {
        try {
            FormularioComplementarService::update(
                $formulario,
                $request->all(),
                FormularioComplementarService::TIPO_FORMULARIO_FEDERACAO
            );

            return redirect()->route('dashboard.formulario-complementar-federacao.index')->with([
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
