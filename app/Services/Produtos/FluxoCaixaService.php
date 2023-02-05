<?php

namespace App\Services\Produtos;

use App\Models\Produtos\ConsignacaoProduto;
use App\Models\Produtos\FluxoCaixa;
use App\Models\Produtos\Produto;

class FluxoCaixaService
{
    public static function store(array $request) : ?FluxoCaixa
    {
        try {
            return FluxoCaixa::create([
                'descricao' => $request['descricao'],
                'valor' => $request['valor'],
                'tipo' => $request['tipo'],
                'comprovante' => $request['comprovante'] ?? null
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function update(FluxoCaixa $fluxo, array $request) : ?FluxoCaixa
    {
        try {
            $fluxo->update([
                'descricao' => $request['descricao'],
                'valor' => $request['valor'],
                'tipo' => $request['tipo'],
                'comprovante' => $request['comprovante'] ?? null
            ]);
            return $fluxo;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(FluxoCaixa $fluxo)
    {
        try {
            $fluxo->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }



    public static function getTotalizadores()
    {
        $total_produtos = 0;
        $valor_produtos = 0;
        $total_consignado = 0;
        $valor_consignado = 0;
        $consignacao = ConsignacaoProduto::all();
        $fluxos = Produto::all();

        $fluxos->map(function($item) use (&$total_produtos) {
            $total_produtos += $item->estoque;
        });

        $fluxos->map(function($item) use (&$valor_produtos) {
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
            'valor_produtos' => $valor_produtos,
            'total_consignado' => $total_consignado,
            'valor_consignado' => $valor_consignado
        ];
    }
}
