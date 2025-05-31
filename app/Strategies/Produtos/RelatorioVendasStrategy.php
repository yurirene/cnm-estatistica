<?php

namespace App\Strategies\Produtos;

use App\Interfaces\RelatorioStrategy;
use App\Models\Produtos\Pedido;
use App\Models\Produtos\Produto;
use App\Services\Produtos\PedidoService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioVendasStrategy implements RelatorioStrategy
{

    private Carbon $dataInicial;
    private Carbon $dataFinal;
    
    public function __construct(string $dataInicial, string $dataFinal)
    {
        $this->dataInicial = Carbon::createFromFormat('d/m/Y', $dataInicial);
        $this->dataFinal = Carbon::createFromFormat('d/m/Y', $dataFinal);
    }
    
    public function gerar(string $tipo)
    {
        $dados = $this->getDados();
        
        return match ($tipo) {
            'csv' => $this->gerarCsv($dados),
            'pdf' => $this->gerarPdf($dados),
            default => throw new \Exception("Tipo de relatório não suportado: {$tipo}", 400),
        };
    }

    public function getDados(): array
    {
        $pedidos = Pedido::whereBetween('created_at', [
                $this->dataInicial->startOfDay(),
                $this->dataFinal->endOfDay()
            ])
            ->where('status', 1)
            ->get();
        $produtosVendidos = [];
        foreach ($pedidos as $pedido) {
            $itens = array_filter(
                json_decode($pedido->produtos, true),
                function ($produto) {
                    return $produto != 0;
                }
            );
            $produtosIds = array_keys($itens);
            $pedido->produtos = Produto::whereIn('id', $produtosIds)
                ->get()
                ->map(function ($produto) use ($itens, &$produtosVendidos) {
                    $produto->quantidade = $itens[$produto->id];
                    $produtosVendidos[$produto->id]['nome'] = $produto->nome;
                    $produtosVendidos[$produto->id]['quantidade'] = ($produtosVendidos[$produto->id]['quantidade'] ?? 0) + $produto->quantidade;

                    return $produto;
                })
                ->toArray();
        }
        $dados['formas_pagamento'] = array_fill_keys(
            array_values(PedidoService::FORMAS_PAGAMENTOS),
            0
        );
        $dados['total_pago'] = 0;
        $dados['total_pedidos'] = $pedidos->count();
        $dados['pedidos'] = $pedidos->map(function ($pedido) use (&$dados) {
                $pedido->data = Carbon::parse($pedido->created_at)->format('d/m/Y H:i:s');
                $pedido->pagamento = PedidoService::FORMAS_PAGAMENTOS[$pedido->forma_pagamento] ?? 'Não Informada';
                $dados['total_pago'] += $pedido->valor_pedido;
                $dados['formas_pagamento'][$pedido->pagamento] += $pedido->valor_pedido;

                return $pedido;
            })
            ->toArray();
        $dados['produtos_vendidos'] = $produtosVendidos;

        return $dados;
    }
    
    public function gerarCsv(array $dados)
    {
        
    }
    public function gerarPdf(array $dados)
    {
        $pdf = Pdf::loadView('dashboard.produtos.relatorio-venda', [
            'dados' => $dados,
            'titulo' => 'Vendas'
        ]);

        $nome = "relatorio_vendas" . Carbon::now()->format('YmdHis');
        return $pdf->download("{$nome}.pdf");
        
    }
}