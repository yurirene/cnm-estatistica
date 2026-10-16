<?php

namespace App\Http\Controllers\Estatistica;

use App\Exceptions\GamificacaoException;
use App\Http\Controllers\Controller;
use App\Services\Gamificacao\GamificacaoAtualizacaoService;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class GamificacaoConfiguracaoController extends Controller
{
    public function __construct(
        private readonly GamificacaoConfiguracaoService $configuracao,
        private readonly GamificacaoAtualizacaoService $atualizacao,
    ) {
    }

    public function index(): View
    {
        return view('dashboard.gamificacao.configuracao', [
            'grupos' => $this->configuracao->formulario(),
            'ligas' => [
                'sinodal' => [
                    'ouro' => $this->configuracao->valor('ligas.sinodal.ouro.faixa'),
                    'prata' => $this->configuracao->valor('ligas.sinodal.prata.faixa'),
                    'bronze' => $this->configuracao->valor('ligas.sinodal.bronze.faixa'),
                ],
                'federacao' => [
                    'ouro' => $this->configuracao->valor('ligas.federacao.ouro.faixa'),
                    'prata' => $this->configuracao->valor('ligas.federacao.prata.faixa'),
                    'bronze' => $this->configuracao->valor('ligas.federacao.bronze.faixa'),
                ],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $this->configuracao->atualizar($request->input('valores', []));

            return redirect()
                ->route('dashboard.game-cnm.index')
                ->with([
                    'mensagem' => [
                        'status' => true,
                        'texto' => 'Configurações do Game CNM atualizadas.',
                    ],
                ]);
        } catch (GamificacaoException $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => $exception->getMessage(),
                    ],
                ]);
        } catch (Throwable) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => self::MSG_ERRO,
                    ],
                ]);
        }
    }

    public function recalcular(): RedirectResponse
    {
        try {
            $total = $this->atualizacao->recalcularTodos(null, null, true);

            return redirect()
                ->route('dashboard.game-cnm.index')
                ->with([
                    'mensagem' => [
                        'status' => true,
                        'texto' => "Placares recalculados: {$total}.",
                    ],
                ]);
        } catch (Throwable) {
            return redirect()
                ->back()
                ->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => self::MSG_ERRO,
                    ],
                ]);
        }
    }
}
