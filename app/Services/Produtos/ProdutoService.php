<?php

namespace App\Services\Produtos;

use App\Models\Produtos\ConsignacaoProduto;
use App\Models\Produtos\Produto;

class ProdutoService
{
    public static function store(array $request) : ?Produto
    {
        try {
            $valor = str_replace(',', '.', str_replace('.', '',$request['valor']));
            return Produto::create([
                'nome' => $request['nome'],
                'valor' => $valor,
                'estoque' => 0
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function update(Produto $produto, array $request) : ?Produto
    {
        try {
            $valor = str_replace(',', '.', str_replace('.', '',$request['valor']));
            $produto->update([
                'nome' => $request['nome'],
                'valor' => $valor
            ]);
            return $produto;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(Produto $produto)
    {
        try {
            $produto->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getAllProdutos()
    {
        return Produto::all();
    }

    public static function getTotalizadores()
    {
        $total_produtos = 0;
        $valor_produtos = 0;
        $total_consignado = 0;
        $valor_consignado = 0;
        $consignacao = ConsignacaoProduto::all();
        $produtos = Produto::all();

        $produtos->map(function($item) use (&$total_produtos) {
            $total_produtos += $item->estoque;
        });

        $produtos->map(function($item) use (&$valor_produtos) {
            $valor_produtos += $item->estoque * $item->valor;
        });



        $consignacao->map(function($item) use (&$total_consignado) {
            $total_consignado += $item->quantidade_consignada - $item->quantidade_retornada;
        });
        $consignacao->map(function($item) use (&$valor_consignado) {
            $quantidade = $item->quantidade_consignada - $item->quantidade_retornada;
            $valor_consignado += $quantidade * $item->produto->valor;
        });
        return [
            'total_produtos' => $total_produtos,
            'valor_produtos' => number_format($valor_produtos, 2, ',', '.'),
            'total_consignado' => $total_consignado,
            'valor_consignado' => number_format($valor_consignado, 2, ',', '.')
        ];
    }
}
