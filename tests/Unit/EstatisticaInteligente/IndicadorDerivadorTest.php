<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Calculo\IndicadorDerivador;
use Tests\TestCase;

class IndicadorDerivadorTest extends TestCase
{
    public function test_crescimento_20_ativos_para_24(): void
    {
        $indicadores = (new IndicadorDerivador())->derivar(
            ['ativos' => 24, 'cooperadores' => 0, 'menor19' => 10, 'de19a23' => 5, 'de24a29' => 5, 'de30a35' => 4],
            ['social' => 1, 'evangelistico' => 1, 'espiritual' => 1, 'recreativo' => 1, 'oracao' => 1],
            ['ativos' => 20],
            ['social' => 1, 'evangelistico' => 1, 'espiritual' => 1, 'recreativo' => 1, 'oracao' => 1],
        );

        $this->assertSame(24, $indicadores['membros_ativos']);
        $this->assertSame(20, $indicadores['membros_ativos_anterior']);
        $this->assertEquals(20.0, $indicadores['crescimento_ativos_anual']);
        $this->assertEquals(62.5, $indicadores['indice_renovacao_geracional']);
        $this->assertSame(5, $indicadores['diversidade_atividades']);
        $this->assertSame('menores_19', $indicadores['faixa_etaria_predominante']);
    }

    public function test_valor_anterior_zero_deixa_crescimento_nulo(): void
    {
        $indicadores = (new IndicadorDerivador())->derivar(
            ['ativos' => 10],
            [],
            ['ativos' => 0],
            [],
        );

        $this->assertSame(0, $indicadores['membros_ativos_anterior']);
        $this->assertNull($indicadores['crescimento_ativos_anual']);
    }

    public function test_le_evangelistica_como_alias_de_evangelistico(): void
    {
        $indicadores = (new IndicadorDerivador())->derivar(
            ['ativos' => 10],
            ['evangelistica' => 4, 'social' => 1],
        );

        $this->assertSame(5, $indicadores['total_atividades']);
        $this->assertEquals(80.0, $indicadores['percentual_atividade_evangelistica']);
    }
}
