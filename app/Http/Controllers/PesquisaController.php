<?php

namespace App\Http\Controllers;

use App\DataTables\PesquisaDataTable;
use App\Exports\PesquisaExport;
use App\Models\Pesquisas\Pesquisa;
use App\Models\Pesquisas\PesquisaConfiguracao;
use App\Models\User;
use App\Services\PesquisaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'instancias' => Pesquisa::INSTANCIAS,
            'secretarios' => User::whereHas('roles', function($sql) {
                return $sql->whereIn('name', ['secretaria_eventos', 'secreatria_produtos', 'secretaria_evangelismo', 'secretaria_responsabilidade']);
            })->get()->pluck('name', 'id')
        ]);
    }

    public function show(Pesquisa $pesquisa)
    {
        try {
            if (in_array(Auth::user()->role->name, User::ROLES_INSTANCIAS)) {
                if (!in_array(Auth::user()->instancia_formatada, $pesquisa->instancias)) {
                    throw new Exception("Sem Permissão para acessar formulário de pesquisa", 1);
                }
            }
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
            'instancias' => Pesquisa::INSTANCIAS,
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
                'alcance' => PesquisaService::getAlcance($pesquisa),
                'mapa_alcance' => PesquisaService::getMapaAlcance($pesquisa),
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
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function limparRespostas(Pesquisa $pesquisa)
    {
        try {
            PesquisaService::limparRespostas($pesquisa);
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

    public function status(Pesquisa $pesquisa)
    {
        try {
            PesquisaService::status($pesquisa);
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


    public function acompanhar(Pesquisa $pesquisa)
    {
        try {
            return view('dashboard.pesquisas.acompanhamento-diretoria', [
                'pesquisa' => $pesquisa,
                'respostas' => PesquisaService::acompanhamentoRegiao($pesquisa)
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
