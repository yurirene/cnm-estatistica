<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Services\Formularios\FormularioComplementarService;
use Illuminate\Http\Request;
use Throwable;

class FormularioComplementarSinodalController extends Controller
{
    public function index()
    {
        $ano = request()->filled('ano') ? request()->get('ano') : date('Y');
        return view('dashboard.formulario-complementar.form', [
            'formulario' => FormularioComplementarService::getFormularioComplementar(
                auth()->user()->sinodal_id,
                FormularioComplementarService::TIPO_FORMULARIO_SINODAL,
                $ano
            ),
            'anos' => FormularioComplementarService::getAnosToSelect(),
            'route' => 'formulario-complementar-sinodal',
            'respostas' => FormularioComplementarService::getRespostas(
                auth()->user()->sinodal_id,
                FormularioComplementarService::TIPO_FORMULARIO_SINODAL,
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
                FormularioComplementarService::TIPO_FORMULARIO_SINODAL
            );

            return redirect()->route('dashboard.formulario-complementar-sinodal.index')->with([
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
