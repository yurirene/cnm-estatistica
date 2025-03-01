<?php

namespace App\Services\Produtos;

use App\Models\Produtos\FluxoEstoqueProduto;
use App\Models\Produtos\Produto;

class EstoqueProdutoService
{
    public const TIPO_ENTRADA = 1;
    public const TIPO_SAIDA = 0;
    public const TIPOS = [
        0 => 'Saída',
        1 => 'Entrada'
    ];
    public const LABELS_TIPOS = [
        0 => 'danger',
        1 => 'success'
    ];

    public static function store(array $request) : ?FluxoEstoqueProduto
    {
        try {
            $fluxo = FluxoEstoqueProduto::create([
                'tipo' => $request['tipo'],
                'quantidade' => $request['quantidade'],
                'observacao' => $request['observacao'],
                'produto_id' => $request['produto_id'],
            ]);

            self::calcularEstoque();

            return $fluxo;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function update(FluxoEstoqueProduto $estoque, array $request) : ?FluxoEstoqueProduto
    {
        try {
            $estoque->update([
                'tipo' => $request['tipo'],
                'quantidade' => $request['quantidade'],
                'observacao' => $request['observacao'],
                'produto_id' => $request['produto_id'],
            ]);

            self::calcularEstoque();

            return $estoque;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(FluxoEstoqueProduto $estoque) : void
    {
        try {
            $estoque->delete();
            self::calcularEstoque();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function calcularEstoque() : void
    {
        try {
            $produtos = Produto::all();

            foreach ($produtos as $produto) {
                $total = 0;
                FluxoEstoqueProduto::where('produto_id', $produto->id)
                ->get()
                ->map(function($item) use (&$total) {
                    if ($item->tipo == 1) {
                        $total += $item->quantidade;
                    } else {
                        $total -= $item->quantidade;
                    }
                });
                $produto->update([
                    'estoque' => $total
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getTipos(bool $is_create = false) : array
    {
        $tipos = self::TIPOS;
        if ($is_create) {
            unset($tipos[2]);
        }
        return $tipos;
    }
}
