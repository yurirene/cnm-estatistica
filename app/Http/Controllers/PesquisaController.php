<?php

namespace App\Http\Controllers;

use App\DataTables\PesquisaDataTable;
use App\Exports\PesquisaExport;
use App\Models\Pesquisa;
use App\Models\PesquisaConfiguracao;
use App\Models\User;
use App\Services\PesquisaService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class PesquisaController extends Controller
{
    public function index(PesquisaDataTable $dataTable)
    {
        return $dataTable->render('dashboard.pesquisas.index');    
    }

    public function create()
    {
        return view('dashboard.pesquisas.form', [
            'secretarios' => User::whereHas('roles', function($sql) {
                return $sql->whereIn('name', ['secretaria_eventos', 'secreatria_produtos', 'secretaria_evangelismo', 'secretaria_responsabilidade']);
            })->get()->pluck('name', 'id')
        ]);
    }

    public function show(Pesquisa $pesquisa)
    {
        try {
            return view('dashboard.pesquisas.show', [
                'pesquisa' => $pesquisa
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

    public function edit(Pesquisa $pesquisa)
    {
        return view('dashboard.pesquisas.form', [
            'pesquisa' => $pesquisa,
            'secretarios' => User::whereHas('roles', function($sql) {
                return $sql->whereIn('name', ['secretaria_eventos', 'secreatria_produtos', 'secretaria_evangelismo', 'secretaria_responsabilidade']);
            })->get()->pluck('name', 'id')
        ]);
    }

    public function store(Request $request)
    {
        try {
            PesquisaService::store($request);
            return redirect()->route('dashboard.pesquisas.index')->with([
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
    
    public function update(Pesquisa $pesquisa, Request $request)
    {
        try {
            PesquisaService::update($pesquisa, $request);
            return redirect()->route('dashboard.pesquisas.index')->with([
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

    public function responder(Request $request)
    {
        try {
            PesquisaService::responder($request);
            return redirect()->route('dashboard.pesquisas.index')->with([
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

    public function configuracoes(Pesquisa $pesquisa)
    {
        try {
            return view('dashboard.pesquisas.configuracoes', [
                'pesquisa' => $pesquisa,
                'configuracoes' => $pesquisa->configuracao,
                'tipos_graficos' => PesquisaConfiguracao::TIPO_GRAFICO,
                'tipos_dados' => PesquisaConfiguracao::TIPO_DADO
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
    

    public function configuracoesUpdate(Pesquisa $pesquisa, Request $request) 
    {
        try {
            PesquisaService::setConfiguracoesPesquisa($pesquisa, $request);
            return redirect()->route('dashboard.pesquisas.configuracoes', $pesquisa->id)->with([
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

    public function relatorio(Pesquisa $pesquisa)
    {
        try {
            return view('dashboard.pesquisas.relatorio', [
                'pesquisa' => $pesquisa,
                'configuracoes' => $pesquisa->configuracao,
                'graficos' => PesquisaService::getGraficos($pesquisa),
                'totalizadores' => PesquisaService::getTotalizadores($pesquisa)
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

    public function exportExcel(Pesquisa $pesquisa)
    {
        try {
            return Excel::download(new PesquisaExport($pesquisa), 'pesquisa_' . $pesquisa->nome . '.xlsx');
        } catch (Throwable $th) {
            dd($th->getMessage());
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
