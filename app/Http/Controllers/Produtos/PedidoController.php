<?php

namespace App\Http\Controllers\Produtos;

use App\Http\Controllers\Controller;
use App\Models\Produtos\Pedido;
use App\Services\Produtos\PedidoService;
use App\Services\Produtos\ProdutoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PedidoController extends Controller
{
    public function index(): View
    {
        return view('dashboard.produtos.pedidos', [
            'produtos' => ProdutoService::getAllProdutos(),
            'pedidos' => PedidoService::getAllPedidos(),
            'formasPagamentos' => PedidoService::FORMAS_PAGAMENTOS 
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            PedidoService::store($request->all());
            return redirect()->route('dashboard.pedidos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Pedido Registrado!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ])
            ->withInput();
        }
    }

    public function pagar(Pedido $pedido, int $formaPagamento): RedirectResponse
    {
        try {
            PedidoService::pagar($pedido, $formaPagamento);
            return redirect()->route('dashboard.pedidos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Pedido Pago!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ])
            ->withInput();
        }
    }

    public function cancelar(Pedido $pedido): RedirectResponse
    {
        try {
            PedidoService::delete($pedido);
            return redirect()->route('dashboard.pedidos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Pedido Cancelado!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ])
            ->withInput();
        }
    }
}
