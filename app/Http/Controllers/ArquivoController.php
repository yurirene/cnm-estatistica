<?php

namespace App\Http\Controllers;

use App\Http\Requests\Arquivos\CriarPastaRequest;
use App\Http\Requests\Arquivos\RenomearItemRequest;
use App\Http\Requests\Arquivos\UploadArquivoRequest;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ArquivoController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (!GoogleDriveService::podeAcessar($request->user())) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Você não tem permissão para acessar os arquivos.',
                ],
            ]);
        }

        if (!GoogleDriveService::temAcesso($request->user())) {
            return view('dashboard.arquivos.index', [
                'semAcesso' => true,
                'driveNaoConfigurado' => !GoogleDriveService::credenciaisGlobaisConfiguradas(),
            ]);
        }

        try {
            $pasta = GoogleDriveService::normalizarPasta($request->query('pasta'));
            $conteudo = GoogleDriveService::listar($request->user(), $pasta);

            return view('dashboard.arquivos.index', [
                'semAcesso' => false,
                'conteudo' => $conteudo,
                'breadcrumbs' => GoogleDriveService::breadcrumbs($pasta),
            ]);
        } catch (Throwable $th) {
            GoogleDriveService::registrarErro($th);

            return view('dashboard.arquivos.index', [
                'semAcesso' => true,
                'erroConexao' => true,
            ]);
        }
    }

    public function upload(UploadArquivoRequest $request): RedirectResponse
    {
        try {
            GoogleDriveService::enviar(
                $request->user(),
                $request->file('arquivo'),
                $request->input('pasta')
            );

            return redirect()->route('dashboard.arquivos.index', [
                'pasta' => GoogleDriveService::normalizarPasta($request->input('pasta')),
            ])->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Arquivo enviado com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            GoogleDriveService::registrarErro($th);

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível enviar o arquivo.',
                ],
            ]);
        }
    }

    public function criarPasta(CriarPastaRequest $request): RedirectResponse
    {
        try {
            GoogleDriveService::criarPasta(
                $request->user(),
                $request->input('nome'),
                $request->input('pasta')
            );

            return redirect()->route('dashboard.arquivos.index', [
                'pasta' => GoogleDriveService::normalizarPasta($request->input('pasta')),
            ])->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Pasta criada com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            GoogleDriveService::registrarErro($th);

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível criar a pasta.',
                ],
            ]);
        }
    }

    public function download(Request $request): Response|RedirectResponse
    {
        return $this->servirArquivo($request, false);
    }

    public function visualizar(Request $request): Response|RedirectResponse
    {
        return $this->servirArquivo($request, true);
    }

    public function renomear(RenomearItemRequest $request): RedirectResponse
    {
        try {
            GoogleDriveService::renomear(
                $request->user(),
                $request->input('caminho'),
                $request->input('nome'),
                $request->input('tipo')
            );

            return redirect()->route('dashboard.arquivos.index', [
                'pasta' => GoogleDriveService::normalizarPasta($request->input('pasta_atual')),
            ])->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Nome atualizado com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            GoogleDriveService::registrarErro($th);

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível renomear o item.',
                ],
            ]);
        }
    }

    private function servirArquivo(Request $request, bool $inline): Response|RedirectResponse
    {
        if (! GoogleDriveService::podeAcessar($request->user())
            || ! GoogleDriveService::temAcesso($request->user())) {
            return redirect()->route('dashboard.home');
        }

        try {
            $arquivo = GoogleDriveService::obterArquivo(
                $request->user(),
                (string) $request->query('caminho')
            );

            $disposition = ($inline ? 'inline' : 'attachment') . '; filename="' . $arquivo['nome'] . '"';

            return response($arquivo['conteudo'], 200, [
                'Content-Type' => $arquivo['mime'],
                'Content-Disposition' => $disposition,
            ]);
        } catch (Throwable $th) {
            GoogleDriveService::registrarErro($th);

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $inline
                        ? 'Não foi possível visualizar o arquivo.'
                        : 'Não foi possível baixar o arquivo.',
                ],
            ]);
        }
    }

    public function excluir(Request $request): RedirectResponse
    {
        if (!GoogleDriveService::podeAcessar($request->user())
            || !GoogleDriveService::temAcesso($request->user())) {
            return redirect()->route('dashboard.home');
        }

        $request->validate([
            'caminho' => ['required', 'string', 'max:500'],
            'tipo' => ['required', 'in:arquivo,pasta'],
            'pasta_atual' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            GoogleDriveService::excluir(
                $request->user(),
                $request->input('caminho'),
                $request->input('tipo')
            );

            return redirect()->route('dashboard.arquivos.index', [
                'pasta' => GoogleDriveService::normalizarPasta($request->input('pasta_atual')),
            ])->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Item excluído com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            GoogleDriveService::registrarErro($th);

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível excluir o item.',
                ],
            ]);
        }
    }
}
