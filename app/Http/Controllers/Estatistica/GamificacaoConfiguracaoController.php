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
                    'simonton' => $this->configuracao->valor('ligas.sinodal.simonton.faixa'),
                    'blackford' => $this->configuracao->valor('ligas.sinodal.blackford.faixa'),
                    'conceicao' => $this->configuracao->valor('ligas.sinodal.conceicao.faixa'),
                ],
                'federacao' => [
                    'simonton' => $this->configuracao->valor('ligas.federacao.simonton.faixa'),
                    'blackford' => $this->configuracao->valor('ligas.federacao.blackford.faixa'),
                    'conceicao' => $this->configuracao->valor('ligas.federacao.conceicao.faixa'),
                ],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $this->configuracao->atualizar($request->input('valores', []));

            return redirect()
                ->route('dashboard.game-ump.index')
                ->with([
                    'mensagem' => [
                        'status' => true,
                        'texto' => 'Configurações do Game UMP atualizadas.',
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
                ->route('dashboard.game-ump.index')
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
