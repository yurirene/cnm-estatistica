<?php

namespace App\Http\Controllers;

use App\DataTables\ConsignacaoProdutosDataTable;
use App\DataTables\EstoqueProdutosDataTable;
use App\DataTables\ProdutosDataTable;
use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(
        ProdutosDataTable $produtosDataTable,
        EstoqueProdutosDataTable $estoqueProdutosDataTable,
        ConsignacaoProdutosDataTable $consignacaoProdutosDataTable
    )
    {
        return view('dashboard.produtos.index', [
            'produtosDataTable' => $produtosDataTable->html(),
            'estoqueProdutosDataTable' => $estoqueProdutosDataTable->html(),
            'consignacaoProdutosDataTable' => $consignacaoProdutosDataTable->html(),
            'totalizadores' => ProdutoService::getTotalizadores()
        ]);
    }

    public function create()
    {
        return view('dashboard.produtos.form');
    }

    public function store(Request $request)
    {
        try {
            ProdutoService::store($request->all());
            return redirect()->route('dashboard.produtos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }
    public function edit(Produto $produto)
    {
        return view('dashboard.produtos.form', [
            'produto' => $produto
        ]);
    }

    public function update(Produto $produto, Request $request)
    {
        try {
            ProdutoService::update($produto, $request->all());
            return redirect()->route('dashboard.produtos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function delete(Produto $produto)
    {
        try {
            ProdutoService::delete($produto);
            return redirect()->route('dashboard.produtos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function produtoDataTable(ProdutosDataTable $produtosDataTable)
    {
        return $produtosDataTable->render('dashboard.produtos.index');
    }

    public function estoqueProdutosDataTable(EstoqueProdutosDataTable $estoqueProdutosDataTable)
    {
        return $estoqueProdutosDataTable->render('dashboard.produtos.index');
    }

    public function consignacaoProdutosDataTable(ConsignacaoProdutosDataTable $consignacaoProdutosDataTable)
    {
        return $consignacaoProdutosDataTable->render('dashboard.produtos.index');
    }
}