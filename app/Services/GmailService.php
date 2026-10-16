<?php

namespace App\Services;

use App\Exceptions\GmailApiException;
use App\Models\User;
use App\Support\GmailClient;
use Google\Service\Exception as GoogleServiceException;
use Google\Service\Gmail\Message;
use Google\Service\Gmail\MessagePart;
use InvalidArgumentException;
use Throwable;

class GmailService
{
    public static function podeAcessar(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if ($user === null || $user->role === null) {
            return false;
        }

        return in_array($user->role->name, User::ROLES_GMAIL, true);
    }

    public static function filtroConfigurado(User $usuario): bool
    {
        return self::montarConsulta($usuario->gmail_alias, $usuario->gmail_label) !== '';
    }

    public static function montarConsulta(?string $alias, ?string $label): string
    {
        $partes = [];
        $alias = trim((string) $alias);
        $label = trim((string) $label);

        if ($alias !== '') {
            $partes[] = 'to:' . self::escaparTermo($alias);
        }

        if ($label !== '') {
            $partes[] = 'label:' . self::escaparTermo($label);
        }

        return implode(' ', $partes);
    }

    /**
     * @return array{gmail_alias: ?string, gmail_label: ?string}
     */
    public static function dadosParaGravar(string $roleName, ?string $alias, ?string $label): array
    {
        if (! in_array($roleName, User::ROLES_GMAIL, true)) {
            return [
                'gmail_alias' => null,
                'gmail_label' => null,
            ];
        }

        $alias = trim((string) $alias);
        $label = trim((string) $label);

        return [
            'gmail_alias' => $alias !== '' ? $alias : null,
            'gmail_label' => $label !== '' ? $label : null,
        ];
    }

    /**
     * @return array{messages: array<int, array<string, string>>, nextPageToken: ?string}
     */
    public static function listar(User $usuario, ?int $maxResults = null, ?string $pageToken = null): array
    {
        if (! self::podeAcessar($usuario)) {
            throw new GmailApiException('Você não tem permissão para listar e-mails.', 403);
        }

        $consulta = self::montarConsulta($usuario->gmail_alias, $usuario->gmail_label);

        if ($consulta === '') {
            throw new GmailApiException('Nenhum alias ou marcador foi configurado para este usuário.', 422);
        }

        $pageToken = trim((string) $pageToken);

        if (strlen($pageToken) > 512) {
            throw new GmailApiException('Página de resultados inválida.', 422);
        }

        $params = [
            'q' => $consulta,
            'maxResults' => self::limitarResultados($maxResults),
        ];

        if ($pageToken !== '') {
            $params['pageToken'] = $pageToken;
        }

        try {
            $lista = GmailClient::gmail()->users_messages->listUsersMessages('me', $params);
            $ids = [];

            foreach ($lista->getMessages() ?? [] as $resumo) {
                $id = $resumo->getId();

                if ($id) {
                    $ids[] = $id;
                }
            }

            return [
                'messages' => self::buscarDetalhes($ids),
                'nextPageToken' => $lista->getNextPageToken() ?: null,
            ];
        } catch (GmailApiException $exception) {
            throw $exception;
        } catch (Throwable $th) {
            self::traduzirErro($th);
        }
    }

    /**
     * @return array{id: string, threadId: string, subject: string, from: string, date: string, snippet: string}
     */
    public static function mapearMensagem(Message $mensagem): array
    {
        $cabecalhos = [];

        foreach ($mensagem->getPayload()?->getHeaders() ?? [] as $header) {
            $nome = strtolower((string) $header->getName());
            $cabecalhos[$nome] = (string) $header->getValue();
        }

        return [
            'id' => (string) $mensagem->getId(),
            'threadId' => (string) $mensagem->getThreadId(),
            'subject' => $cabecalhos['subject'] ?? '',
            'from' => $cabecalhos['from'] ?? '',
            'date' => $cabecalhos['date'] ?? '',
            'snippet' => (string) $mensagem->getSnippet(),
        ];
    }

    /**
     * @param  array<int, string>  $idsDoMarcador
     */
    public static function compativelComFiltro(
        Message $mensagem,
        ?string $alias,
        ?string $label,
        array $idsDoMarcador = []
    ): bool {
        $alias = trim((string) $alias);
        $label = trim((string) $label);

        if ($alias === '' && $label === '') {
            return false;
        }

        if ($alias !== '' && ! self::aliasPresente($mensagem, $alias)) {
            return false;
        }

        if ($label === '') {
            return true;
        }

        $idsMensagem = $mensagem->getLabelIds() ?? [];

        return $idsDoMarcador !== []
            && array_intersect($idsDoMarcador, $idsMensagem) !== [];
    }

    /**
     * @return array{id: string, subject: string, from: string, date: string, attachments: array<int, array{index: int, id: string, filename: string, mimeType: string, size: int}>, html?: string, text?: string}
     */
    public static function montarDetalhe(Message $mensagem): array
    {
        $cabecalhos = self::cabecalhos($mensagem);
        $conteudo = self::extrairConteudo($mensagem);
        $detalhe = [
            'id' => (string) $mensagem->getId(),
            'subject' => $cabecalhos['subject'][0] ?? '',
            'from' => $cabecalhos['from'][0] ?? '',
            'date' => $cabecalhos['date'][0] ?? '',
            'attachments' => $conteudo['attachments'],
        ];

        if ($conteudo['html'] !== null && $conteudo['html'] !== '') {
            $detalhe['html'] = $conteudo['html'];
        } else {
            $detalhe['text'] = $conteudo['text'] ?? '';
        }

        return $detalhe;
    }

    /**
     * @return array{html: ?string, text: ?string, attachments: array<int, array{index: int, id: string, filename: string, mimeType: string, size: int}>}
     */
    public static function extrairConteudo(Message $mensagem): array
    {
        $html = null;
        $texto = null;
        $anexos = [];
        self::percorrerParte($mensagem->getPayload(), $html, $texto, $anexos);

        return [
            'html' => $html,
            'text' => $texto,
            'attachments' => $anexos,
        ];
    }

    public static function decodificarBase64Url(string $dados): string
    {
        $dados = strtr($dados, '-_', '+/');
        $resto = strlen($dados) % 4;

        if ($resto > 0) {
            $dados .= str_repeat('=', 4 - $resto);
        }

        $binario = base64_decode($dados, true);

        return $binario === false ? '' : $binario;
    }

    public static function nomeArquivoSeguro(string $nome, string $mime = ''): string
    {
        $nome = str_replace(["\r", "\n", '"', '\\'], '', $nome);
        $nome = str_replace('/', '-', $nome);
        $nome = trim($nome);

        if ($nome === '') {
            $nome = 'anexo';
        }

        $extensao = self::extensaoPorMime($mime);

        if ($extensao !== '' && preg_match('/\.[a-z0-9]{1,8}$/i', $nome) !== 1) {
            $nome .= '.' . $extensao;
        }

        return $nome;
    }

    /**
     * @return array{id: string, subject: string, from: string, date: string, attachments: array<int, array{index: int, id: string, filename: string, mimeType: string, size: int}>, html?: string, text?: string}
     */
    public static function detalhar(User $usuario, string $id): array
    {
        return self::montarDetalhe(self::carregarMensagemCompleta($usuario, $id));
    }

    /**
     * @return array{conteudo: string, filename: string, mimeType: string}
     */
    public static function baixarAnexo(User $usuario, string $id, string $indice): array
    {
        if (! self::indiceAnexoValido($indice)) {
            throw new GmailApiException('Anexo não encontrado.', 404);
        }

        $mensagem = self::carregarMensagemCompleta($usuario, $id);
        $anexo = null;

        foreach (self::extrairConteudo($mensagem)['attachments'] as $item) {
            if ((string) $item['index'] === $indice) {
                $anexo = $item;
                break;
            }
        }

        if ($anexo === null) {
            throw new GmailApiException('Anexo não encontrado.', 404);
        }

        try {
            $resposta = GmailClient::gmail()->users_messages_attachments->get('me', $id, $anexo['id']);
        } catch (GmailApiException $exception) {
            throw $exception;
        } catch (Throwable $th) {
            self::traduzirFalha($th, 'Não foi possível baixar o anexo.');
        }

        return [
            'conteudo' => self::decodificarBase64Url((string) $resposta->getData()),
            'filename' => self::nomeArquivoSeguro($anexo['filename'], $anexo['mimeType']),
            'mimeType' => $anexo['mimeType'] !== '' ? $anexo['mimeType'] : 'application/octet-stream',
        ];
    }

    public static function registrarErro(Throwable $th): void
    {
        LogErroService::registrar([
            'message' => $th->getMessage(),
            'line' => $th->getLine(),
            'file' => $th->getFile(),
        ]);
    }

    /**
     * @param  array<int, string>  $ids
     * @return array<int, array<string, string>>
     */
    private static function buscarDetalhes(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $gmail = GmailClient::gmail();
        $client = $gmail->getClient();
        $client->setUseBatch(true);

        try {
            $batch = $gmail->createBatch();

            foreach ($ids as $id) {
                $batch->add($gmail->users_messages->get('me', $id, [
                    'format' => 'metadata',
                    'metadataHeaders' => ['Subject', 'From', 'Date'],
                ]), $id);
            }

            $resultados = $batch->execute();
        } finally {
            $client->setUseBatch(false);
        }

        $mensagens = [];

        foreach ($ids as $id) {
            $item = self::resultadoDoLote($resultados, $id);

            if ($item instanceof GoogleServiceException) {
                throw $item;
            }

            if ($item instanceof Message) {
                $mensagens[] = self::mapearMensagem($item);
            }
        }

        return $mensagens;
    }

    private static function resultadoDoLote(array $resultados, string $id): mixed
    {
        foreach ([$id, 'response-' . $id, '<response-' . $id . '>'] as $chave) {
            if (array_key_exists($chave, $resultados)) {
                return $resultados[$chave];
            }
        }

        foreach ($resultados as $chave => $item) {
            $normalizada = trim((string) $chave, '<>');

            if ($normalizada === $id || $normalizada === 'response-' . $id) {
                return $item;
            }
        }

        return null;
    }

    private static function carregarMensagemCompleta(User $usuario, string $id): Message
    {
        if (! self::podeAcessar($usuario)) {
            throw new GmailApiException('Você não tem permissão para ler este e-mail.', 403);
        }

        if (! self::idMensagemValido($id)) {
            throw new GmailApiException('E-mail não encontrado.', 404);
        }

        if (! self::filtroConfigurado($usuario)) {
            throw new GmailApiException('Nenhum alias ou marcador foi configurado para este usuário.', 422);
        }

        try {
            $mensagem = GmailClient::gmail()->users_messages->get('me', $id, [
                'format' => 'full',
            ]);
        } catch (GmailApiException $exception) {
            throw $exception;
        } catch (Throwable $th) {
            self::traduzirFalha($th, 'Não foi possível abrir o e-mail.');
        }

        $idsDoMarcador = [];
        $marcador = trim((string) $usuario->gmail_label);

        if ($marcador !== '') {
            $idsDoMarcador = self::idsDoMarcador($marcador);
        }

        if (! self::compativelComFiltro($mensagem, $usuario->gmail_alias, $usuario->gmail_label, $idsDoMarcador)) {
            throw new GmailApiException('E-mail não encontrado.', 404);
        }

        return $mensagem;
    }

    /**
     * @return array<int, string>
     */
    private static function idsDoMarcador(string $nome): array
    {
        try {
            $lista = GmailClient::gmail()->users_labels->listUsersLabels('me');
        } catch (Throwable $th) {
            self::traduzirFalha($th, 'Não foi possível conferir o marcador.');
        }

        $ids = [];

        foreach ($lista->getLabels() ?? [] as $rotulo) {
            if (strcasecmp((string) $rotulo->getName(), $nome) === 0) {
                $ids[] = (string) $rotulo->getId();
            }
        }

        return $ids;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private static function cabecalhos(Message $mensagem): array
    {
        $cabecalhos = [];

        foreach ($mensagem->getPayload()?->getHeaders() ?? [] as $header) {
            $nome = strtolower((string) $header->getName());
            $cabecalhos[$nome][] = (string) $header->getValue();
        }

        return $cabecalhos;
    }

    private static function aliasPresente(Message $mensagem, string $alias): bool
    {
        $cabecalhos = self::cabecalhos($mensagem);

        foreach (['to', 'cc', 'delivered-to', 'x-original-to'] as $nome) {
            foreach ($cabecalhos[$nome] ?? [] as $valor) {
                if (self::cabecalhoContemEmail($valor, $alias)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function cabecalhoContemEmail(string $valor, string $email): bool
    {
        preg_match_all('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $valor, $encontrados);

        foreach ($encontrados[0] as $encontrado) {
            if (strcasecmp($encontrado, $email) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, array{index: int, id: string, filename: string, mimeType: string, size: int}>  $anexos
     */
    private static function percorrerParte(?MessagePart $parte, ?string &$html, ?string &$texto, array &$anexos): void
    {
        if ($parte === null) {
            return;
        }

        $nomeArquivo = trim((string) $parte->getFilename());
        $corpo = $parte->getBody();
        $idAnexo = $corpo ? trim((string) $corpo->getAttachmentId()) : '';

        if ($nomeArquivo !== '' && $idAnexo !== '') {
            $anexos[] = [
                'index' => count($anexos),
                'id' => $idAnexo,
                'filename' => $nomeArquivo,
                'mimeType' => (string) $parte->getMimeType(),
                'size' => (int) ($corpo?->getSize() ?? 0),
            ];
        } else {
            $mime = strtolower((string) $parte->getMimeType());
            $dados = $corpo ? (string) $corpo->getData() : '';

            if ($dados !== '') {
                $decodificado = self::decodificarBase64Url($dados);

                if ($mime === 'text/html' && $html === null) {
                    $html = $decodificado;
                } elseif ($mime === 'text/plain' && $texto === null) {
                    $texto = $decodificado;
                }
            }
        }

        foreach ($parte->getParts() ?? [] as $filha) {
            self::percorrerParte($filha, $html, $texto, $anexos);
        }
    }

    private static function idMensagemValido(string $id): bool
    {
        return preg_match('/^[A-Za-z0-9_-]{1,128}$/', $id) === 1;
    }

    private static function indiceAnexoValido(string $indice): bool
    {
        return preg_match('/^[0-9]{1,4}$/', $indice) === 1;
    }

    private static function extensaoPorMime(string $mime): string
    {
        return match (strtolower($mime)) {
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
            'application/zip' => 'zip',
            'application/msword' => 'doc',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            default => '',
        };
    }

    private static function traduzirFalha(Throwable $th, string $mensagemPadrao): never
    {
        if ($th instanceof GoogleServiceException && (int) $th->getCode() === 404) {
            self::registrarErro($th);
            throw new GmailApiException('E-mail não encontrado.', 404);
        }

        try {
            self::traduzirErro($th);
        } catch (GmailApiException $exception) {
            if ($exception->getStatusCode() === 502) {
                throw new GmailApiException($mensagemPadrao, 502);
            }

            throw $exception;
        }
    }

    private static function limitarResultados(?int $maxResults): int
    {
        if ($maxResults === null || $maxResults < 1) {
            return 20;
        }

        return min($maxResults, 50);
    }

    private static function escaparTermo(string $valor): string
    {
        $valor = str_replace(['"', "\r", "\n"], '', $valor);

        if (preg_match('/[\s:]/', $valor) === 1) {
            return '"' . $valor . '"';
        }

        return $valor;
    }

    private static function traduzirErro(Throwable $th): never
    {
        self::registrarErro($th);

        if ($th instanceof GoogleServiceException) {
            $code = (int) $th->getCode();

            if ($code === 403) {
                throw new GmailApiException(
                    'A conta de serviço não tem permissão para ler esta caixa. Verifique a delegação de domínio e o escopo gmail.readonly.',
                    403
                );
            }

            if ($code === 429) {
                throw new GmailApiException(
                    'O limite de consultas da Gmail API foi atingido. Tente novamente em alguns minutos.',
                    429
                );
            }

            if (in_array($code, [400, 401], true)) {
                throw new GmailApiException(
                    'As credenciais do Google foram recusadas. Verifique a conta de serviço e o e-mail da caixa.',
                    503
                );
            }
        }

        if ($th instanceof InvalidArgumentException) {
            throw new GmailApiException($th->getMessage(), 503);
        }

        throw new GmailApiException('Não foi possível listar os e-mails.', 502);
    }
}
