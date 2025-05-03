<?php

namespace App\Http\Controllers\Produtos;

use App\Http\Controllers\Controller;
use App\Models\Produtos\Pedido;
use App\Services\Produtos\PedidoService;
use App\Services\Produtos\ProdutoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PedidoController extends Controller
{
    public function index(): View
    {
        return view('dashboard.produtos.pedidos', [
            'produtos' => ProdutoService::getAllProdutos(),
            'formasPagamentos' => PedidoService::FORMAS_PAGAMENTOS 
        ]);
    }
    
    public function caixa(): View
    {
        return view('dashboard.produtos.caixa', [
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
            return redirect()->route('dashboard.pedidos.caixa')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Pedido Pago!'
                ]
            ]);
        } catch (Throwable $th) {
            Log::error('Erro ao pagar pedido:', [
                'msgem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
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
            return redirect()->route('dashboard.pedidos.caixa')->with([
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
    public function separar(Pedido $pedido): RedirectResponse
    {
        try {
            PedidoService::separar($pedido);

            return redirect()->route('dashboard.pedidos.caixa')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Pedido Separado!'
                ]
            ]);
        } catch (Throwable $th) {
            Log::error('Erro ao separar pedido:', [
                'msgem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
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
