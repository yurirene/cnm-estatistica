<?php

namespace App\Support;

use Google\Client;
use Google\Service\Gmail;
use InvalidArgumentException;

class GmailClient
{
    private static ?Gmail $gmail = null;

    public static function configurado(): bool
    {
        return self::resolveJsonPath() !== null && self::emailCaixa() !== null;
    }

    public static function resolveJsonPath(): ?string
    {
        $path = config('gmail.service_account_json');

        if (empty($path)) {
            return null;
        }

        $resolved = str_starts_with($path, DIRECTORY_SEPARATOR)
            ? $path
            : base_path($path);

        return is_readable($resolved) ? $resolved : null;
    }

    public static function emailCaixa(): ?string
    {
        $email = trim((string) config('gmail.impersonate'));

        return $email !== '' ? $email : null;
    }

    public static function gmail(): Gmail
    {
        if (self::$gmail !== null) {
            return self::$gmail;
        }

        $jsonPath = self::resolveJsonPath();
        $email = self::emailCaixa();

        if ($jsonPath === null || $email === null) {
            throw new InvalidArgumentException(
                'Conta de serviço ou e-mail da caixa Gmail não configurados.'
            );
        }

        $client = new Client();
        $client->setAuthConfig($jsonPath);
        $client->setSubject($email);
        $client->addScope(Gmail::GMAIL_READONLY);

        self::$gmail = new Gmail($client);

        return self::$gmail;
    }

    public static function reset(): void
    {
        self::$gmail = null;
    }
}
