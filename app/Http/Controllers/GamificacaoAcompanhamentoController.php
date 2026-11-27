<?php

namespace App\Http\Controllers;

use App\Services\Gamificacao\GamificacaoAcompanhamentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GamificacaoAcompanhamentoController extends Controller
{
    public function __construct(
        private readonly GamificacaoAcompanhamentoService $service
    ) {
    }

    public function externo(Request $request): View
    {
        $painel = $this->service->painel(
            $request->input('tipo'),
            $request->input('periodo')
        );

        return view('dashboard.gamificacao.acompanhamento', [
            'externo' => true,
            'painel' => $painel,
        ]);
    }

    public function dados(Request $request): JsonResponse
    {
        $painel = $this->service->painel(
            $request->input('tipo'),
            $request->input('periodo')
        );

        return response()->json($painel);
    }
}
