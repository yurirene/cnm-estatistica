<?php

namespace App\Http\Controllers;

use App\Exceptions\GmailApiException;
use App\Services\GmailService;
use App\Support\GmailClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class GmailController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $usuario = $request->user();

        if (! GmailService::podeAcessar($usuario)) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Você não tem permissão para acessar os e-mails.',
                ],
            ]);
        }

        if (! GmailClient::configurado()) {
            return view('dashboard.gmail.index', [
                'gmailNaoConfigurado' => true,
            ]);
        }

        if (! GmailService::filtroConfigurado($usuario)) {
            return view('dashboard.gmail.index', [
                'filtroNaoConfigurado' => true,
            ]);
        }

        try {
            $maxResults = $request->query('maxResults');
            $resultado = GmailService::listar(
                $usuario,
                is_numeric($maxResults) ? (int) $maxResults : null,
                $request->query('pageToken')
            );

            return view('dashboard.gmail.index', [
                'mensagens' => $resultado['messages'],
                'nextPageToken' => $resultado['nextPageToken'],
            ]);
        } catch (Throwable $th) {
            if (! $th instanceof GmailApiException) {
                GmailService::registrarErro($th);
            }

            return view('dashboard.gmail.index', [
                'erroConexao' => true,
                'erroMensagem' => $th instanceof GmailApiException
                    ? $th->getMessage()
                    : 'Não foi possível listar os e-mails.',
            ]);
        }
    }

    public function mensagens(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if (! GmailService::podeAcessar($usuario)) {
            return response()->json([
                'message' => 'Você não tem permissão para listar e-mails.',
            ], 403);
        }

        if (! GmailClient::configurado()) {
            return response()->json([
                'message' => 'A Gmail API ainda não foi configurada no sistema.',
            ], 503);
        }

        try {
            $maxResults = $request->query('maxResults');

            return response()->json(GmailService::listar(
                $usuario,
                is_numeric($maxResults) ? (int) $maxResults : null,
                $request->query('pageToken')
            ));
        } catch (GmailApiException $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], $th->getStatusCode());
        } catch (Throwable $th) {
            GmailService::registrarErro($th);

            return response()->json([
                'message' => 'Não foi possível listar os e-mails.',
            ], 502);
        }
    }

    public function show(Request $request, string $mensagem): JsonResponse
    {
        $recusa = $this->recusarAcesso($request);

        if ($recusa !== null) {
            return $recusa;
        }

        try {
            return response()->json(GmailService::detalhar($request->user(), $mensagem));
        } catch (GmailApiException $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], $th->getStatusCode());
        } catch (Throwable $th) {
            GmailService::registrarErro($th);

            return response()->json([
                'message' => 'Não foi possível abrir o e-mail.',
            ], 502);
        }
    }

    public function anexo(Request $request, string $mensagem, string $anexo): Response|JsonResponse
    {
        $recusa = $this->recusarAcesso($request);

        if ($recusa !== null) {
            return $recusa;
        }

        try {
            $arquivo = GmailService::baixarAnexo($request->user(), $mensagem, $anexo);
            $nome = rawurlencode($arquivo['filename']);

            return response($arquivo['conteudo'], 200, [
                'Content-Type' => $arquivo['mimeType'],
                'Content-Disposition' => 'attachment; filename="' . $arquivo['filename'] . '"; filename*=UTF-8\'\'' . $nome,
            ]);
        } catch (GmailApiException $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], $th->getStatusCode());
        } catch (Throwable $th) {
            GmailService::registrarErro($th);

            return response()->json([
                'message' => 'Não foi possível baixar o anexo.',
            ], 502);
        }
    }

    private function recusarAcesso(Request $request): ?JsonResponse
    {
        if (! GmailService::podeAcessar($request->user())) {
            return response()->json([
                'message' => 'Você não tem permissão para ler este e-mail.',
            ], 403);
        }

        if (! GmailClient::configurado()) {
            return response()->json([
                'message' => 'A Gmail API ainda não foi configurada no sistema.',
            ], 503);
        }

        return null;
    }
}
