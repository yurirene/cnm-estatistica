<?php

namespace App\Services;

use App\Models\User;
use App\Support\GoogleDriveClient;
use Exception;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Throwable;

class GoogleDriveService
{
    public const ROLES = [
        'secretariado_comum',
        'diretoria',
        'executiva',
        'presidente',
    ];

    public static function podeAcessar(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return in_array($user->role->name, self::ROLES, true);
    }

    public static function credenciaisGlobaisConfiguradas(): bool
    {
        return GoogleDriveClient::configurado()
            && GoogleDriveClient::pastaPrincipalConfigurada();
    }

    public static function possuiPasta(User $user): bool
    {
        return ! empty($user->google_drive_folder);
    }

    public static function temAcesso(User $user): bool
    {
        return self::credenciaisGlobaisConfiguradas() && self::possuiPasta($user);
    }

    public static function provisionarPastaUsuario(User $user): string
    {
        $rootId = GoogleDriveClient::pastaPrincipalId();
        $nomeBase = self::nomePastaUsuario($user);
        $nome = $nomeBase;
        $sufixo = 2;

        while (GoogleDriveClient::findChildByName($rootId, $nome, true) !== null) {
            $nome = $nomeBase . ' (' . $sufixo . ')';
            $sufixo++;
        }

        $pasta = GoogleDriveClient::createFolder($rootId, $nome);

        return $pasta->getId();
    }

    public static function nomePastaUsuario(User $user): string
    {
        $nome = trim(preg_replace('/[\/\\\\:*?"<>|]+/', '-', $user->name) ?? '');

        if ($nome === '') {
            $nome = 'usuario';
        }

        return $nome;
    }

    public static function sincronizarPastaUsuario(User $user, ?string $folderIdInformado): User
    {
        if (! empty($folderIdInformado)) {
            $user->update(['google_drive_folder' => $folderIdInformado]);

            return $user->fresh();
        }

        if (! empty($user->google_drive_folder)) {
            return $user;
        }

        if (! in_array($user->role->name, User::ROLES_ARQUIVOS, true)) {
            return $user;
        }

        $folderId = self::provisionarPastaUsuario($user);
        $user->update(['google_drive_folder' => $folderId]);

        return $user->fresh();
    }

    public static function pastaRaiz(User $user): string
    {
        if (! self::possuiPasta($user)) {
            throw new Exception('Pasta do Google Drive não configurada para este usuário.');
        }

        return $user->google_drive_folder;
    }

    public static function normalizarPasta(?string $pasta): string
    {
        $pasta = trim(str_replace('\\', '/', (string) $pasta), '/');

        if (str_contains($pasta, '..')) {
            throw new Exception('Caminho inválido.');
        }

        return $pasta;
    }

    public static function montarCaminho(string $pasta, string $nome): string
    {
        $pasta = self::normalizarPasta($pasta);
        $nome = trim(str_replace(['/', '\\'], '', $nome));

        if ($nome === '') {
            throw new Exception('Nome inválido.');
        }

        return $pasta !== '' ? $pasta . '/' . $nome : $nome;
    }

    public static function listar(User $user, ?string $pasta = null): array
    {
        $pasta = self::normalizarPasta($pasta);
        $folderId = GoogleDriveClient::folderId(self::pastaRaiz($user), $pasta);
        $items = GoogleDriveClient::listChildren($folderId);

        $pastas = [];
        $arquivos = [];

        foreach ($items as $item) {
            $caminho = self::montarCaminho($pasta, $item->getName());

            if (GoogleDriveClient::isFolder($item)) {
                $pastas[] = [
                    'nome' => $item->getName(),
                    'caminho' => $caminho,
                    'tipo' => 'pasta',
                ];

                continue;
            }

            $arquivos[] = [
                'nome' => $item->getName(),
                'caminho' => $caminho,
                'tipo' => 'arquivo',
                'tamanho' => (int) ($item->getSize() ?? 0),
                'modificado' => strtotime($item->getModifiedTime() ?? 'now'),
            ];
        }

        usort($pastas, fn ($a, $b) => strcasecmp($a['nome'], $b['nome']));
        usort($arquivos, fn ($a, $b) => strcasecmp($a['nome'], $b['nome']));

        return [
            'pasta_atual' => $pasta,
            'pastas' => $pastas,
            'arquivos' => $arquivos,
        ];
    }

    public static function enviar(User $user, UploadedFile $arquivo, ?string $pasta = null): string
    {
        $pasta = self::normalizarPasta($pasta);
        $folderId = GoogleDriveClient::folderId(self::pastaRaiz($user), $pasta);
        $nome = $arquivo->getClientOriginalName();

        GoogleDriveClient::uploadFile(
            $folderId,
            $nome,
            file_get_contents($arquivo->getRealPath()),
            $arquivo->getMimeType() ?: self::mimeType($nome)
        );

        return self::montarCaminho($pasta, $nome);
    }

    public static function criarPasta(User $user, string $nome, ?string $pasta = null): string
    {
        $pasta = self::normalizarPasta($pasta);
        $nome = trim(str_replace(['/', '\\'], '', $nome));

        if ($nome === '') {
            throw new Exception('Informe o nome da pasta.');
        }

        $parentId = GoogleDriveClient::folderId(self::pastaRaiz($user), $pasta);
        GoogleDriveClient::createFolder($parentId, $nome);

        return self::montarCaminho($pasta, $nome);
    }

    public static function excluir(User $user, string $caminho, string $tipo): void
    {
        $caminho = self::normalizarPasta($caminho);

        if ($caminho === '') {
            throw new Exception('Caminho inválido.');
        }

        $file = self::resolverItem($user, $caminho, $tipo === 'pasta');

        if ($file === null) {
            throw new Exception('Item não encontrado.');
        }

        GoogleDriveClient::deleteFile($file->getId());
    }

    public static function renomear(User $user, string $caminho, string $novoNome, string $tipo): void
    {
        $caminho = self::normalizarPasta($caminho);
        $novoNome = trim(str_replace(['/', '\\'], '', $novoNome));

        if ($caminho === '' || $novoNome === '') {
            throw new Exception('Nome inválido.');
        }

        $file = self::resolverItem($user, $caminho, $tipo === 'pasta');

        if ($file === null) {
            throw new Exception('Item não encontrado.');
        }

        GoogleDriveClient::rename($file->getId(), $novoNome);
    }

    public static function obterArquivo(User $user, string $caminho): array
    {
        $caminho = self::normalizarPasta($caminho);

        if ($caminho === '') {
            throw new Exception('Arquivo não encontrado.');
        }

        $file = GoogleDriveClient::findFileByPath(self::pastaRaiz($user), $caminho);

        if ($file === null) {
            throw new Exception('Arquivo não encontrado.');
        }

        return [
            'conteudo' => GoogleDriveClient::downloadFile($file->getId()),
            'nome' => $file->getName(),
            'mime' => $file->getMimeType() ?: self::mimeType($caminho),
        ];
    }

    public static function resolverItem(User $user, string $caminho, bool $pasta): ?DriveFile
    {
        $parts = explode('/', $caminho);
        $name = array_pop($parts);
        $parentPath = implode('/', $parts);
        $parentId = GoogleDriveClient::folderId(self::pastaRaiz($user), $parentPath);

        return GoogleDriveClient::findChildByName($parentId, $name, $pasta);
    }

    public static function mimeType(string $caminho): string
    {
        $extensao = strtolower(pathinfo($caminho, PATHINFO_EXTENSION));

        return match ($extensao) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            default => 'application/octet-stream',
        };
    }

    public static function breadcrumbs(?string $pasta): array
    {
        $pasta = self::normalizarPasta($pasta);

        if ($pasta === '') {
            return [];
        }

        $partes = explode('/', $pasta);
        $caminho = '';
        $breadcrumbs = [];

        foreach ($partes as $parte) {
            $caminho = $caminho === '' ? $parte : $caminho . '/' . $parte;
            $breadcrumbs[] = [
                'nome' => $parte,
                'caminho' => $caminho,
            ];
        }

        return $breadcrumbs;
    }

    public static function registrarErro(Throwable $th): void
    {
        LogErroService::registrar([
            'message' => $th->getMessage(),
            'line' => $th->getLine(),
            'file' => $th->getFile(),
        ]);
    }
}
