<?php

namespace Tests\Unit;

use App\Services\MeetService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class MeetServiceTest extends TestCase
{
    public function test_corpo_abre_sala_sem_moderador(): void
    {
        $this->assertSame([
            'config' => [
                'accessType' => 'OPEN',
                'entryPointAccess' => 'ALL',
            ],
        ], MeetService::corpoEspaco());
    }

    public function test_rejeita_inicio_no_passado(): void
    {
        $agora = new DateTimeImmutable('2026-10-16 15:00:00');

        $erros = MeetService::validarHorario(
            $agora->modify('-1 minute'),
            $agora->modify('+1 hour'),
            $agora
        );

        $this->assertSame('O início não pode estar no passado.', $erros['inicia_em']);
    }

    public function test_rejeita_fim_antes_do_inicio(): void
    {
        $agora = new DateTimeImmutable('2026-10-16 15:00:00');

        $erros = MeetService::validarHorario(
            $agora->modify('+2 hours'),
            $agora->modify('+1 hour'),
            $agora
        );

        $this->assertSame('O fim precisa ser depois do início.', $erros['termina_em']);
    }

    public function test_rejeita_duracao_maior_que_oito_horas(): void
    {
        $agora = new DateTimeImmutable('2026-10-16 15:00:00');

        $erros = MeetService::validarHorario(
            $agora->modify('+1 hour'),
            $agora->modify('+10 hours'),
            $agora
        );

        $this->assertSame('A reunião pode durar no máximo 8 horas.', $erros['termina_em']);
    }

    public function test_aceita_horario_valido(): void
    {
        $agora = new DateTimeImmutable('2026-10-16 15:00:00');

        $erros = MeetService::validarHorario(
            $agora->modify('+1 hour'),
            $agora->modify('+2 hours'),
            $agora
        );

        $this->assertSame([], $erros);
    }
}
