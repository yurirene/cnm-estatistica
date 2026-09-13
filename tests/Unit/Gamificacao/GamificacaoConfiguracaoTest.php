<?php

namespace Tests\Unit\Gamificacao;

use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Tests\TestCase;

class GamificacaoConfiguracaoTest extends TestCase
{
    public function test_catalogo_expoe_maximo_dos_pilares(): void
    {
        $nomes = array_keys((new GamificacaoConfiguracaoService())->camposPorNome());

        foreach (Pilar::cases() as $pilar) {
            $this->assertContains("pilares.{$pilar->value}.max", $nomes);
        }
    }

    public function test_sincronizar_derivados_atualiza_legais_e_faixas_de_liga(): void
    {
        $service = app(GamificacaoConfiguracaoService::class);
        $backup = [
            'pilares.estatistica.max' => $service->valor('pilares.estatistica.max'),
            'pilares.evangelismo.faixas' => $service->valor('pilares.evangelismo.faixas'),
            'ligas.sinodal.ouro.min' => $service->valor('ligas.sinodal.ouro.min'),
            'ligas.sinodal.prata.min' => $service->valor('ligas.sinodal.prata.min'),
            'ciclo_inicio' => $service->valor('ciclo_inicio'),
            'ciclo_fim' => $service->valor('ciclo_fim'),
        ];

        try {
            $service->hidratarParaTeste([
                'pilares.estatistica.max' => 40,
                'pilares.evangelismo.faixas' => [100 => 40, 80 => 20, 60 => 10],
                'ligas.sinodal.ouro.min' => 400,
                'ligas.sinodal.prata.min' => 200,
                'ciclo_inicio' => 2026,
                'ciclo_fim' => 2029,
            ]);

            $this->assertSame([0, 40], $service->valor('pilares.estatistica.legais'));
            $this->assertSame(40, $service->valor('pilares.evangelismo.max'));
            $this->assertSame([0, 10, 20, 40], $service->valor('pilares.evangelismo.legais'));
            $this->assertSame(399, $service->valor('ligas.sinodal.prata.max'));
            $this->assertSame(199, $service->valor('ligas.sinodal.bronze.max'));
            $this->assertSame(4, $service->valor('anos_ciclo'));
            $this->assertSame(40, Pilar::Estatistica->maximo());
        } finally {
            $service->hidratarParaTeste($backup);
        }
    }
}
