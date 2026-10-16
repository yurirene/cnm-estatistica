<?php

namespace Tests\Unit\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Services\Gamificacao\Guards\EscritorPlacar;
use App\Services\Gamificacao\Guards\PlacarGuard;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\Gamificacao\Regras\ValoresLegaisPilar;
use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use Tests\TestCase;

class PlacarGuardTest extends TestCase
{
    private function contexto(array $over = []): ContextoInstancia
    {
        $base = [
            'natureza' => NaturezaInstancia::Sinodal,
            'sinodalId' => 'sin-1',
            'federacaoId' => null,
            'ano' => 2026,
            'ciclo' => '2026-2030',
            'sociosAtivos' => 10,
            'totalFilhos' => 1,
            'filhosEntregaram' => 1,
            'percentualEstatistica' => 100,
            'aciComprovanteAnexado' => true,
            'aciRepasseInformado' => true,
            'aciValorRepassado' => 1,
            'aciValorPrevisto' => 1,
            'umpsTotal' => 1,
            'umpsComProgramacao' => 1,
            'percentualEvangelismo' => 100,
            'ceConfirmada' => false,
            'temBonusMissionario' => false,
            'temResgate' => false,
            'dataEntregaEstatistica' => null,
            'pontosEventosPorTipo' => [],
        ];

        return new ContextoInstancia(...array_merge($base, $over));
    }

    public function test_rejeita_instancia_dupla(): void
    {
        $this->expectException(GamificacaoException::class);
        $guard = new PlacarGuard(new ValoresLegaisPilar(), new CalendarioGamificacao());
        $guard->assertInstanciaValida($this->contexto([
            'sinodalId' => 's',
            'federacaoId' => 'f',
        ]));
    }

    public function test_rejeita_natureza_cruzada(): void
    {
        $this->expectException(GamificacaoException::class);
        $guard = new PlacarGuard(new ValoresLegaisPilar(), new CalendarioGamificacao());
        $guard->assertInstanciaValida($this->contexto([
            'natureza' => NaturezaInstancia::Federacao,
            'sinodalId' => 's',
            'federacaoId' => null,
        ]));
    }

    public function test_escritor_aninhado(): void
    {
        $this->assertFalse(EscritorPlacar::liberado());
        EscritorPlacar::executar(function () {
            $this->assertTrue(EscritorPlacar::liberado());
            EscritorPlacar::executar(function () {
                $this->assertTrue(EscritorPlacar::liberado());
            });
            $this->assertTrue(EscritorPlacar::liberado());
        });
        $this->assertFalse(EscritorPlacar::liberado());
    }
}
