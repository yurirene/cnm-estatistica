<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Calculo\PorteClassifier;
use Tests\TestCase;

class PorteClassifierTest extends TestCase
{
    public function test_faixas_da_spec(): void
    {
        $classifier = PorteClassifier::fromConfig();

        $this->assertSame('01', $classifier->classificar(1));
        $this->assertSame('01', $classifier->classificar(10));
        $this->assertSame('03', $classifier->classificar(24));
        $this->assertSame('06', $classifier->classificar(121));
        $this->assertNull($classifier->classificar(0));
    }
}
