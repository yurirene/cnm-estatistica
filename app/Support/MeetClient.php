<?php

namespace App\Support;

use App\Exceptions\MeetApiException;
use App\Services\LogErroService;
use Google\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Throwable;

class MeetClient
{
    public const ESCOPO = 'https://www.googleapis.com/auth/meetings.space.created';

    private static ?Client $client = null;

    public static function configurado(): bool
    {
        return self::resolveJsonPath() !== null && self::emailOrganizador() !== null;
    }

    public static function resolveJsonPath(): ?string
    {
        $path = config('meet.service_account_json');

        if (empty($path)) {
            return null;
        }

        $resolved = str_starts_with($path, DIRECTORY_SEPARATOR)
            ? $path
            : base_path($path);

        return is_readable($resolved) ? $resolved : null;
    }

    public static function emailOrganizador(): ?string
    {
        $email = trim((string) config('meet.impersonate'));

        return $email !== '' ? $email : null;
    }

    /**
     * @param  array<string, mixed>  $corpo
     * @return array<string, mixed>
     */
    public static function criarEspaco(array $corpo): array
    {
        try {
            $resposta = self::client()->authorize()->post('https://meet.googleapis.com/v2/spaces', [
                'json' => $corpo,
            ]);
        } catch (MeetApiException $exception) {
            throw $exception;
        } catch (RequestException $th) {
            self::traduzirFalha($th);
        } catch (Throwable $th) {
            self::registrarErro($th);
            throw new MeetApiException('Não foi possível criar a sala do Meet.', 502);
        }

        $dados = json_decode((string) $resposta->getBody(), true);

        if (! is_array($dados) || empty($dados['meetingUri'])) {
            throw new MeetApiException('A resposta do Meet não trouxe o link da sala.', 502);
        }

        return $dados;
    }

    public static function reset(): void
    {
        self::$client = null;
    }

    private static function client(): Client
    {
        if (self::$client !== null) {
            return self::$client;
        }

        $jsonPath = self::resolveJsonPath();
        $email = self::emailOrganizador();

        if ($jsonPath === null || $email === null) {
            throw new InvalidArgumentException(
                'Conta de serviço ou e-mail organizador do Meet não configurados.'
            );
        }

        $client = new Client();
        $client->setAuthConfig($jsonPath);
        $client->setSubject($email);
        $client->addScope(self::ESCOPO);

        self::$client = $client;

        return self::$client;
    }

    private static function traduzirFalha(RequestException $th): never
    {
        self::registrarErro($th);
        $codigo = (int) ($th->getResponse()?->getStatusCode() ?? 0);

        if ($codigo === 403) {
            throw new MeetApiException(
                'A conta de serviço não tem permissão para criar salas do Meet. Verifique a delegação de domínio e o escopo meetings.space.created.',
                403
            );
        }

        if ($codigo === 429) {
            throw new MeetApiException('A cota da API do Meet foi atingida. Tente de novo em alguns minutos.', 429);
        }

        if (in_array($codigo, [400, 401], true)) {
            throw new MeetApiException('As credenciais do Meet foram recusadas.', 503);
        }

        throw new MeetApiException('Não foi possível criar a sala do Meet.', 502);
    }

    private static function registrarErro(Throwable $th): void
    {
        LogErroService::registrar([
            'message' => $th->getMessage(),
            'line' => $th->getLine(),
            'file' => $th->getFile(),
        ]);
    }
}
