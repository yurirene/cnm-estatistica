<?php

namespace App\Http\Controllers\Instancias;

use App\DataTables\Instancias\FederacaoDataTable;
use App\Http\Controllers\Controller;
use App\Models\Federacao;
use App\Services\Instancias\FederacaoService;
use Illuminate\Http\Request;
use Throwable;

class FederacaoController extends Controller
{

    public function index(FederacaoDataTable $dataTable)
    {
        return $dataTable->render('dashboard.federacoes.index');
    }

    public function create()
    {
        $estados = FederacaoService::getEstados();
        $sinodais = FederacaoService::getSinodal();
        return view('dashboard.federacoes.form', [
            'estados' => $estados,
            'sinodais' => $sinodais
        ]);
    }

    public function store(Request $request)
    {
        try {
            FederacaoService::store($request);
            return redirect()->route('dashboard.federacoes.index')->with([
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

    public function show(Federacao $federacao)
    {
        return view('dashboard.federacoes.show', [
            'umps' => FederacaoService::getInformacoesLocaisShow($federacao),
            'informacoes' => FederacaoService::getInformacoesFederacaoOrganizacao($federacao),
            'federacao' => $federacao,
        ]);
    }

    public function edit(Federacao $federacao)
    {
        $estados = FederacaoService::getEstados();
        $sinodais = FederacaoService::getSinodal();
        return view('dashboard.federacoes.form', [
            'federacao' => $federacao,
            'estados' => $estados,
            'sinodais' => $sinodais
        ]);
    }

    public function update(Federacao $federacao, Request $request)
    {
        try {
            FederacaoService::update($federacao, $request);
            return redirect()->route('dashboard.federacoes.index')->with([
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


    public function updateInfo(Federacao $federacao, Request $request)
    {
        try {
            FederacaoService::updateInfo($federacao, $request);
            return redirect()->route('dashboard.home')->with([
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

    public function delete(Federacao $federacao)
    {
        try {
            FederacaoService::delete($federacao);
            return redirect()->route('dashboard.sinodais.index')->with([
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
