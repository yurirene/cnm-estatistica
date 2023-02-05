<?php

namespace App\Http\Controllers\Produtos;

use App\DataTables\Produtos\FluxoCaixaDataTable;
use App\Http\Controllers\Controller;
use App\Models\Produtos\FluxoCaixa;
use App\Services\Produtos\FluxoCaixaService;
use Illuminate\Http\Request;
use Throwable;

class FluxoCaixaController extends Controller
{

    public function create()
    {
        try {
            return view('dashboard.produtos.fluxo-caixa.form', [
                'tipos' => FluxoCaixa::TIPOS_ATIVOS
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function edit(FluxoCaixa $fluxo)
    {
        try {
            return view('dashboard.produtos.fluxo-caixa.form', [
                'fluxo' => $fluxo,
                'tipos' => FluxoCaixa::TIPOS_ATIVOS
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            FluxoCaixaService::store($request->all());
            return redirect()->route('dashboard.produtos.index')->with([
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
                ],
                'aba' => 3
            ])
            ->withInput();
        }
    }

    public function update(FluxoCaixa $fluxo, Request $request)
    {
        try {
            FluxoCaixaService::update($fluxo, $request->all());
            return redirect()->route('dashboard.produtos.index')->with([
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
                ],
                'aba' => 3
            ])
            ->withInput();
        }
    }

    public function delete(FluxoCaixa $fluxo)
    {
        try {
            FluxoCaixaService::delete($fluxo);
            return redirect()->route('dashboard.produtos.index')->with([
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
                ],
                'aba' => 3
            ])
            ->withInput();
        }
    }

    public function fluxoCaixaDataTable(FluxoCaixaDataTable $fluxoCaixaDataTable)
    {
        return $fluxoCaixaDataTable->render('dashboard.produtos.index');
    }
}
