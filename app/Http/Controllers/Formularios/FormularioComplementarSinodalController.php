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
        return view('dashboard.formulario-complementar.form', [
            'formulario' => FormularioComplementarService::getFormularioComplementar(
                auth()->user()->sinodal_id,
                FormularioComplementarService::TIPO_FORMULARIO_SINODAL
            ),
            'route' => 'formulario-complementar-sinodal',
            'respostas' => FormularioComplementarService::getRespostas(
                auth()->user()->sinodal_id,
                FormularioComplementarService::TIPO_FORMULARIO_SINODAL
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

    // public function configuracoes()
    // {
    //     try {
    //         return view('dashboard.formulario-complementar-sinodal.configuracoes', [
    //             'pesquisa' => $pesquisa,
    //             'configuracoes' => $pesquisa->configuracao,
    //             'tipos_graficos' => FormularioComplementarService::TIPO_GRAFICO,
    //             'tipos_dados' => FormularioComplementarService::TIPO_DADO
    //         ]);
    //     } catch (Throwable $th) {
    //         return redirect()->back()->with([
    //             'mensagem' => [
    //                 'status' => false,
    //                 'texto' => 'Algo deu Errado!'
    //             ]
    //         ])
    //         ->withInput();
    //     }
    // }

    // public function configuracoesUpdate(Pesquisa $pesquisa, Request $request)
    // {
    //     try {
    //         PesquisaService::setConfiguracoesPesquisa($pesquisa, $request);
    //         return redirect()->route('dashboard.formulario-complementar-sinodal.configuracoes', $pesquisa->id)->with([
    //             'mensagem' => [
    //                 'status' => true,
    //                 'texto' => 'Operação realizada com Sucesso!'
    //             ]
    //         ]);
    //     } catch (Throwable $th) {
    //         return redirect()->back()->with([
    //             'mensagem' => [
    //                 'status' => false,
    //                 'texto' => 'Algo deu Errado!'
    //             ]
    //         ])
    //         ->withInput();
    //     }
    // }

    // public function exportExcel(Pesquisa $pesquisa)
    // {
    //     try {
    //         return Excel::download(new PesquisaExport($pesquisa), 'pesquisa_' . $pesquisa->nome . '.xlsx');
    //     } catch (Throwable $th) {
    //         return redirect()->back()->with([
    //             'mensagem' => [
    //                 'status' => false,
    //                 'texto' => 'Algo deu Errado!'
    //             ]
    //         ])
    //         ->withInput();
    //     }
    // }

    // public function status(Pesquisa $pesquisa)
    // {
    //     try {
    //         PesquisaService::status($pesquisa);
    //         return redirect()->route('dashboard.formulario-complementar-sinodal.index')->with([
    //             'mensagem' => [
    //                 'status' => true,
    //                 'texto' => 'Operação realizada com Sucesso!'
    //             ]
    //         ]);
    //     } catch (Throwable $th) {
    //         return redirect()->back()->with([
    //             'mensagem' => [
    //                 'status' => false,
    //                 'texto' => 'Algo deu Errado!'
    //             ]
    //         ])
    //         ->withInput();
    //     }
    // }
}
