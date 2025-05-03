<?php

namespace App\Services\Produtos;

use App\Models\Produtos\ConsignacaoProduto;
use App\Models\Produtos\FluxoCaixa;
use App\Models\Produtos\Pedido;
use App\Models\Produtos\Produto;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ProdutoService
{
    public static function store(array $request) : ?Produto
    {
        try {
            $valor = str_replace(',', '.', str_replace('.', '',$request['valor']));
            return Produto::create([
                'nome' => $request['nome'],
                'valor' => $valor,
                'estoque' => 0,
                'exibir' => $request['exibir']
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
                'valor' => $valor,
                'exibir' => $request['exibir']
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
        $produtos = Produto::where('exibir', true)->get();
        
        return self::calcularEstoqueTravado($produtos); 
    }

    public static function calcularEstoqueTravado($produtos)
    {
        foreach ($produtos as &$produto) {
            $totalProdutos = $produto->estoque;
            Pedido::where('status', 0)
                ->get()
                ->filter(function($item) use ($produto) {
                    $produtosDoPedido = json_decode($item->produtos, true);
                    
                    return isset($produtosDoPedido[$produto->id]);
                })
                ->each(function ($item) use (&$totalProdutos, $produto) {
                    $pedido = json_decode($item->produtos, true);
                    $totalProdutos -= $pedido[$produto->id];
                });
                
            $produto->estoqueTravado = $totalProdutos;
        }

        return $produtos;
    }

    public static function getTotalizadoresProdutos()
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

    public static function getTotalizadores()
    {
        try {
            $mesAtual = Carbon::today();
            $mesAnterior = Carbon::today()->subYear();
            $periodos = CarbonPeriod::create($mesAnterior, '1 month', $mesAtual);
            $meses = [];
            foreach ($periodos as $periodo) {
                $inicio = $periodo->startOfMonth()->format('Y-m-d');
                $fim = $periodo->endOfMonth()->format('Y-m-d');
                $meses[$periodo->format('m-Y')] = FluxoCaixa::whereIn('tipo', [FluxoCaixa::ENTRADA, FluxoCaixa::SAIDA])
                    ->whereBetween('data_lancamento', [$inicio, $fim])
                    ->get();
            }
            $retorno = [];
            $saldoInicial = FluxoCaixa::where('tipo', FluxoCaixa::SALDO_INICIAL)->first();
            $total = !is_null($saldoInicial) ? $saldoInicial->getRawOriginal('valor') : 0;
            foreach ($meses as $key => $lancamentos) {
                foreach ($lancamentos as $lancamento) {
                    if ($lancamento->tipo == FluxoCaixa::ENTRADA) {
                        $total += $lancamento->getRawOriginal('valor');
                    } else {
                        $total -= $lancamento->getRawOriginal('valor');
                    }
                }
                $retorno[$key] = $total;
            }
            return self::montarGraficoLinha($retorno);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Montar o gráfico de linhas de lançamentos no fluxo de caixa
     *
     * @param array $dados
     * @return array
     */
    public static function montarGraficoLinha(array $dados): array
    {
        $grafico = [
            "type" => 'line',
            "data" => [],
            "options" => [
                "elements" => [
                    "line" => [
                        "borderWidth" => 5
                    ]
                ],
                "responsive" => true,
                    "plugins" => [
                    "legend" => [
                        "position" => 'top',
                    ],
                    "title" => [
                        "display" => true,
                        "text" => 'Gráfico Fluxo de Caixa'
                    ]
                ]
            ]
        ];
        $grafico["data"] = [
            "labels" => array_keys($dados),
            "datasets" => [
                [
                    'label' => 'Saldo (R$)',
                    'data' => array_values($dados),
                    'borderColor' => '#ffa600',
                    'backgroundColor' => '#172b4d',
                    'tension' => 0.4
                ],
            ]
        ];
        return $grafico;
    }
}
