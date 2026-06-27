<?php

namespace App\Support;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use InvalidArgumentException;

class GoogleDriveClient
{
    private static ?Drive $drive = null;

    public static function configurado(): bool
    {
        return self::resolveJsonPath() !== null;
    }

    public static function resolveJsonPath(): ?string
    {
        $path = config('google-drive.service_account_json');

        if (empty($path)) {
            return null;
        }

        $resolved = str_starts_with($path, DIRECTORY_SEPARATOR)
            ? $path
            : base_path($path);

        return is_readable($resolved) ? $resolved : null;
    }

    public static function drive(): Drive
    {
        if (self::$drive !== null) {
            return self::$drive;
        }

        $jsonPath = self::resolveJsonPath();

        if ($jsonPath === null) {
            throw new InvalidArgumentException('Arquivo JSON da conta de serviço do Google Drive não configurado.');
        }

        $client = new Client();
        $client->setAuthConfig($jsonPath);
        $client->addScope(Drive::DRIVE);

        self::$drive = new Drive($client);

        return self::$drive;
    }

    public static function reset(): void
    {
        self::$drive = null;
    }

    public static function escapeQuery(string $value): string
    {
        return str_replace(["\\", "'"], ["\\\\", "\\'"], $value);
    }

    public static function isFolder(DriveFile $file): bool
    {
        return $file->getMimeType() === 'application/vnd.google-apps.folder';
    }

    public static function folderId(string $rootFolderId, ?string $relativePath): string
    {
        $relativePath = trim(str_replace('\\', '/', (string) $relativePath), '/');

        if ($relativePath === '') {
            return $rootFolderId;
        }

        if (str_contains($relativePath, '..')) {
            throw new InvalidArgumentException('Caminho inválido.');
        }

        $parentId = $rootFolderId;

        foreach (explode('/', $relativePath) as $segment) {
            if ($segment === '') {
                continue;
            }

            $parentId = self::findChildFolderId($parentId, $segment);

            if ($parentId === null) {
                throw new InvalidArgumentException('Pasta não encontrada.');
            }
        }

        return $parentId;
    }

    public static function findChildFolderId(string $parentId, string $name): ?string
    {
        $file = self::findChildByName($parentId, $name, true);

        return $file?->getId();
    }

    public static function findFileByPath(string $rootFolderId, string $relativePath): ?DriveFile
    {
        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');

        if ($relativePath === '') {
            return null;
        }

        $parts = explode('/', $relativePath);
        $fileName = array_pop($parts);
        $folderPath = implode('/', $parts);
        $parentId = self::folderId($rootFolderId, $folderPath);

        return self::findChildByName($parentId, $fileName, false);
    }

    public static function findChildByName(string $parentId, string $name, bool $folder): ?DriveFile
    {
        $escapedName = self::escapeQuery($name);
        $query = sprintf("'%s' in parents and name = '%s' and trashed = false", $parentId, $escapedName);

        if ($folder) {
            $query .= " and mimeType = 'application/vnd.google-apps.folder'";
        } else {
            $query .= " and mimeType != 'application/vnd.google-apps.folder'";
        }

        $response = self::drive()->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name, mimeType, size, modifiedTime)',
            'pageSize' => 1,
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
        ]);

        $files = $response->getFiles();

        return $files[0] ?? null;
    }

    public static function listChildren(string $folderId): array
    {
        $response = self::drive()->files->listFiles([
            'q' => sprintf("'%s' in parents and trashed = false", $folderId),
            'fields' => 'files(id, name, mimeType, size, modifiedTime)',
            'orderBy' => 'folder,name',
            'pageSize' => 200,
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
        ]);

        return $response->getFiles();
    }

    public static function pastaPrincipalId(): string
    {
        $id = config('google-drive.root_folder_id');

        if (empty($id)) {
            throw new InvalidArgumentException(
                'Pasta principal do Google Drive não configurada (GOOGLE_DRIVE_ROOT_FOLDER_ID).'
            );
        }

        return $id;
    }

    public static function pastaPrincipalConfigurada(): bool
    {
        return ! empty(config('google-drive.root_folder_id'));
    }

    public static function createFolder(string $parentId, string $name): DriveFile
    {
        $metadata = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId],
        ]);

        return self::drive()->files->create($metadata, [
            'fields' => 'id, name',
            'supportsAllDrives' => true,
        ]);
    }

    public static function uploadFile(string $parentId, string $name, string $contents, string $mimeType): DriveFile
    {
        $metadata = new DriveFile([
            'name' => $name,
            'parents' => [$parentId],
        ]);

        return self::drive()->files->create($metadata, [
            'data' => $contents,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id, name',
            'supportsAllDrives' => true,
        ]);
    }

    public static function downloadFile(string $fileId): string
    {
        $response = self::drive()->files->get($fileId, [
            'alt' => 'media',
            'supportsAllDrives' => true,
        ]);

        if (is_string($response)) {
            return $response;
        }

        if (method_exists($response, 'getBody')) {
            return $response->getBody()->getContents();
        }

        throw new InvalidArgumentException('Não foi possível baixar o arquivo do Google Drive.');
    }

    public static function deleteFile(string $fileId): void
    {
        self::drive()->files->delete($fileId, [
            'supportsAllDrives' => true,
        ]);
    }

    public static function rename(string $fileId, string $newName): DriveFile
    {
        $metadata = new DriveFile([
            'name' => $newName,
        ]);

        return self::drive()->files->update($fileId, $metadata, [
            'fields' => 'id, name',
            'supportsAllDrives' => true,
        ]);
    }
}
