<?php

namespace App\Services\Produtos;

use App\Models\Produtos\Pedido;
use App\Models\Produtos\FluxoCaixa;
use App\Models\Produtos\Produto;
use App\Services\Produtos\EstoqueProdutoService;
use App\Services\Produtos\FluxoCaixaService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoService
{

    public const FORMAS_PAGAMENTOS = [
        1 => 'Pix',
        2 => 'Cartão Crédito',
        3 => 'Cartão Débito',
        4 => 'Dinheiro'
    ];

    public static function store(array $request) : ?Pedido
    {
        try {
            DB::beginTransaction();

            $valor = str_replace(',', '.', str_replace('.', '',$request['total_pedido']));

            foreach ($request['produtos'] as $id => $quantidade) {
                $produto = Produto::findOrFail($id);

                if ($produto->estoque < $quantidade) {
                    throw new Exception("Não há quantidade suficiente do produto: {$produto->nome}");
                }
            }

            $pedido = Pedido::create([
                'nome' => $request['nome'],
                'vendedor' => $request['vendedor'],
                'comanda' => $request['comanda'],
                'forma_pagamento' => $request['forma_pagamento'],
                'valor_pedido' => $valor,
                'produtos' => json_encode($request['produtos']),
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return $pedido;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function pagar(Pedido $pedido, int $formaPagamento): void
    {
        try {
            DB::beginTransaction();

            $stringPagamento = 'Venda de: ';

            foreach (json_decode($pedido->produtos, true) as $id => $quantidade) {

                if ($quantidade == 0) {
                    continue;
                }

                $produto = Produto::findOrFail($id);

                if ($produto->estoque < $quantidade) {
                    throw new Exception("Não há quantidade suficiente do produto: {$produto->nome}");
                }

                EstoqueProdutoService::store([
                    'tipo' => EstoqueProdutoService::TIPO_SAIDA,
                    'quantidade' => $quantidade,
                    'observacao' => "Venda para {$pedido->nome} - pedido#{$pedido->id}",
                    'produto_id' => $id
                ]);

                $stringPagamento .= "{$quantidade} und - {$produto->nome}, ";
            }
            $formaPagamentoFormatado = self::FORMAS_PAGAMENTOS[$formaPagamento];

            if ($pedido->forma_pagamento != $formaPagamento) {
                $pedido->update([
                    'forma_pagamento' => $formaPagamento
                ]);
            }

            FluxoCaixaService::store([
                'descricao' => "{$stringPagamento} para {$pedido->nome} - {$formaPagamentoFormatado}",
                'valor' => (float) $pedido->valor_pedido,
                'tipo' => FluxoCaixa::ENTRADA,
                'data_lancamento' => date('d/m/Y')
            ]);

            $pedido->update([
                'status' => true,
            ]);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Erro ao Pagar', [
                'msg' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);

            throw $th;
        }
    }

    public static function delete(Pedido $pedido)
    {
        $pedido->delete();
    }

    public static function getAllPedidos(): array
    {
        $pedidos = Pedido::when(
            !request()->filled('listar_todas'),
            function ($sql) {
                return $sql->where('status', false);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        $retorno = [];

        foreach ($pedidos as $pedido) {
            $produtoPedido = json_decode($pedido->produtos, true);
            $produtos = [];

            foreach ($produtoPedido as $id => $quantidade) {
                if ($quantidade == 0) {
                    continue;
                }

                $produto = Produto::find($id);
                $produtos[] = "{$quantidade} und - {$produto->nome}";
            }

            $retorno[] = [
                'id' => $pedido->id,
                'comanda' => $pedido->comanda,
                'vendedor' => $pedido->vendedor,
                'nome' => $pedido->nome,
                'produtos' => $produtos,
                'valor' => $pedido->valor_pedido,
                'status' => $pedido->status,
                'forma_pagamento' => $pedido->forma_pagamento,
                'pagamento' => self::FORMAS_PAGAMENTOS[$pedido->forma_pagamento]
            ];

        }
        return $retorno;
    }

}
