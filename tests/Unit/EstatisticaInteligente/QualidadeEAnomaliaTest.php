<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Calculo\AnomaliaDetector;
use App\Services\EstatisticaInteligente\Calculo\QualidadeCalculator;
use Tests\TestCase;

class QualidadeEAnomaliaTest extends TestCase
{
    public function test_anomalia_nos_limites(): void
    {
        $detector = new AnomaliaDetector(30, 50);

        $this->assertSame([], $detector->detectar(29.9));
        $this->assertSame('variacao_anual', $detector->detectar(30.0)[0]['tipo']);
        $this->assertSame('variacao_extrema', $detector->detectar(50.0)[0]['tipo']);
        $this->assertSame([], $detector->detectar(null));
    }

    public function test_qualidade_penaliza_falta_de_historico_e_anomalia(): void
    {
        $calc = QualidadeCalculator::fromConfig();
        $perfil = [
            'ativos' => 10,
            'cooperadores' => 0,
            'menor19' => 4,
            'de19a23' => 2,
            'de24a29' => 2,
            'de30a35' => 2,
        ];

        $completa = $calc->score($perfil, true, []);
        $semHistorico = $calc->score($perfil, false, []);
        $comAnomalia = $calc->score($perfil, true, [['tipo' => 'variacao_extrema']]);

        $this->assertEquals(100.0, $completa);
        $this->assertEquals(80.0, $semHistorico);
        $this->assertEquals(90.0, $comAnomalia);
    }
}
