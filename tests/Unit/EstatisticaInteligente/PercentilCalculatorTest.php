<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Calculo\PercentilCalculator;
use Tests\TestCase;

class PercentilCalculatorTest extends TestCase
{
    public function test_percentil_com_tres_pares_da_federacao(): void
    {
        $calc = new PercentilCalculator();

        $this->assertEquals(100.0, $calc->calcular(30, [10, 20, 30]));
        $this->assertEqualsWithDelta(66.666, $calc->calcular(20, [10, 20, 30]), 0.01);
        $this->assertEqualsWithDelta(33.333, $calc->calcular(10, [10, 20, 30]), 0.01);
    }

    public function test_menos_de_dois_pares_retorna_nulo(): void
    {
        $this->assertNull((new PercentilCalculator())->calcular(10, [10]));
        $this->assertNull((new PercentilCalculator())->calcular(10, []));
    }
}
