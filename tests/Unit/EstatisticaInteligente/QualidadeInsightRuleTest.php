<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Regras\FaixasNarrativas;
use App\Services\EstatisticaInteligente\Regras\QualidadeInsightRule;
use Tests\TestCase;

class QualidadeInsightRuleTest extends TestCase
{
    use MontaContexto;

    private QualidadeInsightRule $regra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regra = new QualidadeInsightRule(new FaixasNarrativas());
    }

    public function test_nao_emite_evento_sem_score(): void
    {
        $this->assertSame([], $this->regra->avaliar($this->contexto()));
    }

    public function test_qualidade_alta_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'qualidade' => ['score' => 85.0],
        ]));

        $this->assertSame([EventoNarrativoEnum::QUALIDADE_ALTA->value], $this->codigos($eventos));
    }

    public function test_qualidade_media_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'qualidade' => ['score' => 60.0],
        ]));

        $this->assertSame([EventoNarrativoEnum::QUALIDADE_MEDIA->value], $this->codigos($eventos));
    }

    public function test_qualidade_media_84_99(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'qualidade' => ['score' => 84.99],
        ]));

        $this->assertSame([EventoNarrativoEnum::QUALIDADE_MEDIA->value], $this->codigos($eventos));
    }

    public function test_qualidade_baixa(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'qualidade' => ['score' => 59.99],
        ]));

        $this->assertSame([EventoNarrativoEnum::QUALIDADE_BAIXA->value], $this->codigos($eventos));
    }
}
