<?php

namespace App\Http\Controllers\Estatistica;

use App\Http\Controllers\Controller;
use App\Services\Estatistica\PlanejamentoEstrategicoService;
use Illuminate\Http\Request;

class PlanejamentoEstrategicoController extends Controller
{
    public function __construct(
        private readonly PlanejamentoEstrategicoService $service
    ) {
    }

    public function externo(Request $request)
    {
        $ano = $request->integer('ano') ?: null;
        $regiao = $this->normalizarRegiao($request);
        $painel = $this->service->painel($ano, $regiao);

        return view('dashboard.index.planejamento-estrategico.index', [
            'externo' => true,
            'painel' => $painel,
        ]);
    }

    public function dados(Request $request)
    {
        $ano = $request->integer('ano') ?: null;
        $regiao = $this->normalizarRegiao($request);

        return response()->json($this->service->painel($ano, $regiao), 200);
    }

    private function normalizarRegiao(Request $request): ?int
    {
        $regiao = $request->input('regiao');
        if ($regiao === null || $regiao === '') {
            return null;
        }

        $regiao = (int) $regiao;

        return $regiao > 0 ? $regiao : null;
    }
}
