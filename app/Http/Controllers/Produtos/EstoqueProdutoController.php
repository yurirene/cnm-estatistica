<?php

namespace App\Http\Controllers;

use App\Models\Produtos\FluxoEstoqueProduto;
use App\Services\Produtos\EstoqueProdutoService;
use App\Services\Produtos\ProdutoService;
use Illuminate\Http\Request;

class EstoqueProdutoController extends Controller
{

    public function create()
    {
        return view('dashboard.produtos.estoque-form', [
            'produtos' => ProdutoService::getAllProdutos()->pluck('nome', 'id'),
            'tipos' => EstoqueProdutoService::getTipos(true)
        ]);
    }

    public function store(Request $request)
    {
        try {
            EstoqueProdutoService::store($request->all());
            return redirect()->route('dashboard.produtos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ],
                'aba' => 1
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ],
                'aba' => 1
            ])
            ->withInput();
        }
    }
    public function edit(FluxoEstoqueProduto $estoque)
    {
        return view('dashboard.produtos.estoque-form', [
            'estoque' => $estoque,
            'produtos' => ProdutoService::getAllProdutos()->pluck('nome', 'id'),
            'tipos' => EstoqueProdutoService::getTipos(true)
        ]);
    }

    public function update(FluxoEstoqueProduto $estoque, Request $request)
    {
        try {
            EstoqueProdutoService::update($estoque, $request->all());
            return redirect()->route('dashboard.produtos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ],
                'aba' => 1
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ],
                'aba' => 1
            ])
            ->withInput();
        }
    }

    public function delete(FluxoEstoqueProduto $estoque)
    {
        try {
            EstoqueProdutoService::delete($estoque);
            return redirect()->route('dashboard.produtos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ],
                'aba' => 1
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ],
                'aba' => 1
            ])
            ->withInput();
        }
    }
}
