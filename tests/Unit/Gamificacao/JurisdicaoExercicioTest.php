<?php

namespace Tests\Unit\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Models\Gamificacao\Placar;
use App\Services\Gamificacao\Guards\PlacarGuard;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\Gamificacao\Regras\JurisdicaoExercicioResolver;
use App\Services\Gamificacao\Regras\ValoresLegaisPilar;
use Carbon\Carbon;
use Tests\TestCase;

class JurisdicaoExercicioTest extends TestCase
{
    public function test_art_41_ato_ate_junho_vale_no_mesmo_ano(): void
    {
        $resolver = new JurisdicaoExercicioResolver();

        $this->assertSame(2027, $resolver->anoEfeito(Carbon::parse('2027-06-30')));
        $this->assertSame(2027, $resolver->anoEfeito(Carbon::parse('2027-01-15')));
    }

    public function test_art_41_ato_apos_junho_vale_no_ano_seguinte(): void
    {
        $resolver = new JurisdicaoExercicioResolver();

        $this->assertSame(2028, $resolver->anoEfeito(Carbon::parse('2027-07-01')));
        $this->assertSame(2028, $resolver->anoEfeito(Carbon::parse('2027-12-31')));
    }

    public function test_transferencia_apos_30_06_nao_muda_exercicio_corrente(): void
    {
        $resolver = new JurisdicaoExercicioResolver();
        $transferencias = [
            (object) [
                'created_at' => Carbon::parse('2027-08-10'),
                'federacao_origem_id' => 'fed-origem',
                'sinodal_origem_id' => 'sin-origem',
                'federacao_destino_id' => 'fed-destino',
                'sinodal_destino_id' => 'sin-destino',
            ],
        ];

        $vinculo2027 = $resolver->aplicarTransferencias(
            'fed-destino',
            'sin-destino',
            2027,
            $transferencias
        );
        $vinculo2028 = $resolver->aplicarTransferencias(
            'fed-destino',
            'sin-destino',
            2028,
            $transferencias
        );

        $this->assertSame('fed-origem', $vinculo2027['federacao_id']);
        $this->assertSame('sin-origem', $vinculo2027['sinodal_id']);
        $this->assertSame('fed-destino', $vinculo2028['federacao_id']);
        $this->assertSame('sin-destino', $vinculo2028['sinodal_id']);
    }

    public function test_transferencia_ate_30_06_ja_conta_no_destino_no_mesmo_ano(): void
    {
        $resolver = new JurisdicaoExercicioResolver();
        $transferencias = [
            (object) [
                'created_at' => Carbon::parse('2027-03-10'),
                'federacao_origem_id' => 'fed-origem',
                'sinodal_origem_id' => 'sin-origem',
                'federacao_destino_id' => 'fed-destino',
                'sinodal_destino_id' => 'sin-destino',
            ],
        ];

        $vinculo = $resolver->aplicarTransferencias(
            'fed-destino',
            'sin-destino',
            2027,
            $transferencias
        );

        $this->assertSame('fed-destino', $vinculo['federacao_id']);
        $this->assertSame('sin-destino', $vinculo['sinodal_id']);
    }

    public function test_cadeia_de_transferencias_respeita_efeito_por_exercicio(): void
    {
        $resolver = new JurisdicaoExercicioResolver();
        // Mais recentes primeiro
        $transferencias = [
            (object) [
                'created_at' => Carbon::parse('2028-08-01'),
                'federacao_origem_id' => 'fed-b',
                'sinodal_origem_id' => 'sin-b',
            ],
            (object) [
                'created_at' => Carbon::parse('2027-02-01'),
                'federacao_origem_id' => 'fed-a',
                'sinodal_origem_id' => 'sin-a',
            ],
        ];

        // Estado atual: fed-c / sin-c (após transferência de ago/2028 com efeito 2029)
        $atualFed = 'fed-c';
        $atualSin = 'sin-c';

        $this->assertSame('fed-a', $resolver->aplicarTransferencias($atualFed, $atualSin, 2026, $transferencias)['federacao_id']);
        $this->assertSame('fed-b', $resolver->aplicarTransferencias($atualFed, $atualSin, 2027, $transferencias)['federacao_id']);
        $this->assertSame('fed-b', $resolver->aplicarTransferencias($atualFed, $atualSin, 2028, $transferencias)['federacao_id']);
        $this->assertSame('fed-c', $resolver->aplicarTransferencias($atualFed, $atualSin, 2029, $transferencias)['federacao_id']);
    }

    public function test_placar_fechado_rejeita_forcar_sem_reabrir(): void
    {
        $this->expectException(GamificacaoException::class);
        $this->expectExceptionMessage('exercício está fechado');

        $placar = new Placar();
        $placar->fechado_em = now();

        $guard = new PlacarGuard(new ValoresLegaisPilar(), new CalendarioGamificacao());
        $guard->assertAnoAberto($placar, true, false);
    }

    public function test_placar_fechado_aceita_reabrir(): void
    {
        $placar = new Placar();
        $placar->fechado_em = now();

        $guard = new PlacarGuard(new ValoresLegaisPilar(), new CalendarioGamificacao());
        $guard->assertAnoAberto($placar, true, true);

        $this->assertTrue(true);
    }
}
