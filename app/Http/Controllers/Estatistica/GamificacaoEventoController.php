<?php

namespace App\Http\Controllers\Estatistica;

use App\Exceptions\GamificacaoException;
use App\Http\Controllers\Controller;
use App\Models\Federacao;
use App\Models\Gamificacao\Conquista;
use App\Models\Sinodal;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\GamificacaoConquistaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class GamificacaoEventoController extends Controller
{
    public function __construct(
        private readonly GamificacaoConquistaService $conquistas,
    ) {
    }

    public function index(Request $request): View
    {
        $ano = (int) ($request->input('ano') ?: EstatisticaService::getAnoReferencia());
        $conquistas = $this->conquistas->listar($ano);
        $grupos = $conquistas->groupBy(function (Conquista $conquista) {
            return $conquista->tipo->value . '|' . ($conquista->referencia ?? '') . '|' . $conquista->ano_referencia;
        });

        $federacoes = Federacao::query()
            ->where('status', true)
            ->with('sinodal:id,nome')
            ->orderBy('nome')
            ->get(['id', 'nome', 'sinodal_id']);

        $federacoesPorSinodal = $federacoes
            ->groupBy(fn (Federacao $federacao) => $federacao->sinodal?->nome ?: 'Sem sinodal');

        $sinodais = Sinodal::query()
            ->where('status', true)
            ->orderBy('nome')
            ->pluck('nome', 'id');

        $tipos = collect(TipoConquista::cases())
            ->sortBy(fn (TipoConquista $tipo) => $tipo->natureza() === NaturezaInstancia::Sinodal ? 1 : 0)
            ->mapWithKeys(fn (TipoConquista $tipo) => [
                $tipo->value => [
                    'label' => $tipo->label(),
                    'natureza' => $tipo->natureza()->value,
                    'pontos' => $tipo->pontos(),
                    'unico' => $tipo->unicoNoAno(),
                ],
            ]);

        return view('dashboard.gamificacao.eventos', [
            'ano' => $ano,
            'anos' => range(EstatisticaService::getAnoReferencia(), EstatisticaService::getAnoReferencia() - 3),
            'grupos' => $grupos,
            'federacoesPorSinodal' => $federacoesPorSinodal,
            'sinodais' => $sinodais,
            'tipos' => $tipos,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tipos = array_column(TipoConquista::cases(), 'value');
        $dados = $request->validate([
            'tipo' => ['required', Rule::in($tipos)],
            'referencia' => ['nullable', 'string', 'max:255'],
            'ano' => ['required', 'integer', 'min:2020'],
            'federacao_ids' => ['nullable', 'array'],
            'federacao_ids.*' => ['uuid'],
            'sinodal_ids' => ['nullable', 'array'],
            'sinodal_ids.*' => ['uuid'],
        ]);

        try {
            $total = $this->conquistas->conceder(
                TipoConquista::from($dados['tipo']),
                $dados['federacao_ids'] ?? [],
                $dados['sinodal_ids'] ?? [],
                (string) ($dados['referencia'] ?? ''),
                (int) $dados['ano']
            );

            return redirect()
                ->route('dashboard.game-cnm.eventos.index', ['ano' => $dados['ano']])
                ->with([
                    'mensagem' => [
                        'status' => true,
                        'texto' => $total === 1
                            ? 'Participação lançada.'
                            : "{$total} participações lançadas.",
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

    public function destroy(string $conquista): RedirectResponse
    {
        $registro = Conquista::withoutGlobalScopes()->findOrFail($conquista);
        $ano = (int) $registro->ano_referencia;

        try {
            $this->conquistas->revogar($registro);

            return redirect()
                ->route('dashboard.game-cnm.eventos.index', ['ano' => $ano])
                ->with([
                    'mensagem' => [
                        'status' => true,
                        'texto' => 'Participação removida.',
                    ],
                ]);
        } catch (GamificacaoException $exception) {
            return redirect()
                ->back()
                ->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => $exception->getMessage(),
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
