<?php

namespace App\Services;

use App\Exceptions\MeetApiException;
use App\Models\MeetSala;
use App\Models\User;
use App\Support\MeetClient;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;
use Throwable;

class MeetService
{
    /**
     * @return array{config: array{accessType: string, entryPointAccess: string}}
     */
    public static function corpoEspaco(): array
    {
        return [
            'config' => [
                'accessType' => 'OPEN',
                'entryPointAccess' => 'ALL',
            ],
        ];
    }

    public static function podeAcessar(?User $usuario): bool
    {
        $papel = $usuario?->role?->name;

        return is_string($papel) && in_array($papel, User::ROLES_MEET, true);
    }

    /**
     * @return array<string, string>
     */
    public static function validarHorario(DateTimeInterface $inicio, DateTimeInterface $fim, DateTimeInterface $agora): array
    {
        $erros = [];
        $inicioTs = $inicio->getTimestamp();
        $fimTs = $fim->getTimestamp();

        if ($inicioTs < $agora->getTimestamp()) {
            $erros['inicia_em'] = 'O início não pode estar no passado.';
        }

        if ($fimTs <= $inicioTs) {
            $erros['termina_em'] = 'O fim precisa ser depois do início.';
        } elseif (($fimTs - $inicioTs) > 8 * 3600) {
            $erros['termina_em'] = 'A reunião pode durar no máximo 8 horas.';
        }

        return $erros;
    }

    public static function solicitar(
        User $solicitante,
        string $paraUserId,
        string $titulo,
        DateTimeInterface $inicio,
        DateTimeInterface $fim
    ): MeetSala {
        if (! self::podeAcessar($solicitante)) {
            throw new MeetApiException('Você não tem permissão para criar uma sala.', 403);
        }

        $erros = self::validarHorario($inicio, $fim, new DateTimeImmutable('now'));

        if ($erros !== []) {
            throw ValidationException::withMessages($erros);
        }

        $para = User::query()->with('role')->find($paraUserId);

        if (! $para instanceof User || ! self::podeAcessar($para)) {
            throw ValidationException::withMessages([
                'para_user_id' => 'Escolha um usuário da diretoria, presidência ou secretarias.',
            ]);
        }

        $espaco = MeetClient::criarEspaco(self::corpoEspaco());

        return MeetSala::query()->create([
            'user_id' => $solicitante->id,
            'para_user_id' => $para->id,
            'titulo' => $titulo,
            'inicia_em' => $inicio,
            'termina_em' => $fim,
            'meet_name' => (string) ($espaco['name'] ?? ''),
            'meet_uri' => (string) $espaco['meetingUri'],
            'meet_code' => (string) ($espaco['meetingCode'] ?? ''),
        ]);
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
