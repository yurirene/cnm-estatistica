<?php

namespace Tests\Unit;

use App\Models\ValorAciAno;
use Tests\TestCase;

class ValorAciAnoTest extends TestCase
{
    public function test_parse_valor_aceita_formatos_numericos_e_brasileiros(): void
    {
        $this->assertSame(24.0, ValorAciAno::parseValor(24));
        $this->assertSame(24.5, ValorAciAno::parseValor(24.5));
        $this->assertSame(24.0, ValorAciAno::parseValor('24'));
        $this->assertSame(24.0, ValorAciAno::parseValor('24,00'));
        $this->assertSame(24.5, ValorAciAno::parseValor('24,50'));
        $this->assertSame(1234.56, ValorAciAno::parseValor('1.234,56'));
        $this->assertSame(0.0, ValorAciAno::parseValor(''));
    }
}
