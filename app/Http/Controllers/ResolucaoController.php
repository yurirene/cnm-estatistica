<?php

namespace App\Http\Controllers;

use App\DataTables\ResolucoesDataTable;
use App\Http\Requests\Resolucoes\ImportResolucaoRequest;
use App\Http\Requests\Resolucoes\StoreResolucaoRequest;
use App\Http\Requests\Resolucoes\UpdateResolucaoRequest;
use App\Http\Requests\Resolucoes\UpdateTelegramChatIdRequest;
use App\Models\Resolucao;
use App\Services\ResolucaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ResolucaoController extends Controller
{
    public function index(ResolucoesDataTable $dataTable)
    {
        try {
            $opcoes = ResolucaoService::opcoesEnums();

            return $dataTable->render('dashboard.secretaria-executiva.resolucoes.index', [
                'estatisticas' => ResolucaoService::estatisticas(),
                'podeGerenciar' => ResolucaoService::isGestor(),
                'origens' => $opcoes['origens'],
                'status' => $opcoes['status'],
                'prioridades' => $opcoes['prioridades'],
                'usuario' => auth()->user(),
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu errado!',
                ],
            ]);
        }
    }

    public function store(StoreResolucaoRequest $request): RedirectResponse
    {
        try {
            ResolucaoService::criar(
                $request->validated(),
                $request->user(),
                $request->file('anexos', [])
            );

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Resolução criada com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível criar a resolução.',
                ],
            ])->withInput();
        }
    }

    public function update(UpdateResolucaoRequest $request, Resolucao $resolucao): RedirectResponse
    {
        try {
            ResolucaoService::atualizar(
                $resolucao,
                $request->safe()->except(['anexos', 'remover_anexos']),
                $request->file('anexos', []),
                $request->input('remover_anexos', [])
            );

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Resolução atualizada com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível atualizar a resolução.',
                ],
            ])->withInput();
        }
    }

    public function destroy(Resolucao $resolucao): RedirectResponse
    {
        try {
            if (!ResolucaoService::isGestor()) {
                abort(403);
            }

            ResolucaoService::excluir($resolucao);

            return redirect()->route('dashboard.secretaria-executiva.resolucoes.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Resolução removida com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível remover a resolução.',
                ],
            ]);
        }
    }

    public function importar(ImportResolucaoRequest $request): RedirectResponse
    {
        try {
            if (!ResolucaoService::isGestor()) {
                abort(403);
            }

            $resultado = ResolucaoService::importarCsv($request->file('arquivo'), $request->user());
            $texto = "{$resultado['importados']} resolução(ões) importada(s).";

            if (!empty($resultado['erros'])) {
                $texto .= ' Erros: ' . implode(' | ', array_slice($resultado['erros'], 0, 5));

                if (count($resultado['erros']) > 5) {
                    $texto .= ' ...';
                }
            }

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => empty($resultado['erros']) || $resultado['importados'] > 0,
                    'texto' => $texto,
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Falha na importação do arquivo.',
                ],
            ]);
        }
    }

    public function modeloImportacao(): StreamedResponse
    {
        $caminho = storage_path('app/templates/resolucoes-importacao.csv');

        if (!file_exists($caminho)) {
            abort(404);
        }

        return response()->streamDownload(
            fn () => readfile($caminho),
            'modelo-importacao-resolucoes.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function responsaveis(): JsonResponse
    {
        return response()->json(['results' => ResolucaoService::getResponsaveis()]);
    }

    public function atualizarTelegram(UpdateTelegramChatIdRequest $request): RedirectResponse
    {
        $request->user()->update([
            'telegram_chat_id' => $request->input('telegram_chat_id'),
        ]);

        return redirect()->back()->with([
            'mensagem' => [
                'status' => true,
                'texto' => 'Telegram configurado com sucesso.',
            ],
        ]);
    }
}
