<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Regras\AnomaliaInsightRule;
use Tests\TestCase;

class AnomaliaInsightRuleTest extends TestCase
{
    use MontaContexto;

    private AnomaliaInsightRule $regra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regra = new AnomaliaInsightRule();
    }

    public function test_nao_emite_evento_sem_anomalias(): void
    {
        $this->assertSame([], $this->regra->avaliar($this->contexto()));
    }

    public function test_emite_um_unico_evento_mesmo_com_varias_anomalias(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'anomalias' => [
                ['tipo' => 'variacao_extrema'],
                ['tipo' => 'desvio_historico'],
            ],
        ]));

        $this->assertCount(1, $eventos);
        $this->assertSame(EventoNarrativoEnum::ANOMALIA_DETECTADA->value, $eventos[0]->codigo->value);
        $this->assertSame(2, $eventos[0]->variaveis['quantidade']);
    }
}
