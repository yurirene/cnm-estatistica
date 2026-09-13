<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Regras\ComparativoInsightRule;
use App\Services\EstatisticaInteligente\Regras\FaixasNarrativas;
use Tests\TestCase;

class ComparativoInsightRuleTest extends TestCase
{
    use MontaContexto;

    private ComparativoInsightRule $regra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regra = new ComparativoInsightRule(new FaixasNarrativas());
    }

    public function test_nao_emite_evento_sem_percentil(): void
    {
        $this->assertSame([], $this->regra->avaliar($this->contexto()));
    }

    public function test_acima_mediana_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'comparativo' => ['percentil' => 75.0],
        ]));

        $this->assertSame([EventoNarrativoEnum::ACIMA_MEDIANA_PORTE->value], $this->codigos($eventos));
    }

    public function test_74_99_na_mediana(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'comparativo' => ['percentil' => 74.99],
        ]));

        $this->assertSame([EventoNarrativoEnum::NA_MEDIANA_PORTE->value], $this->codigos($eventos));
    }

    public function test_25_na_mediana(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'comparativo' => ['percentil' => 25.0],
        ]));

        $this->assertSame([EventoNarrativoEnum::NA_MEDIANA_PORTE->value], $this->codigos($eventos));
    }

    public function test_abaixo_mediana(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'comparativo' => ['percentil' => 24.99],
        ]));

        $this->assertSame([EventoNarrativoEnum::ABAIXO_MEDIANA_PORTE->value], $this->codigos($eventos));
    }

    public function test_salto_abaixo_para_acima_emite_mudanca_relevante(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'comparativo' => [
                'percentil' => 80.0,
                'percentil_anterior' => 20.0,
            ],
        ]));

        $this->assertSame([
            EventoNarrativoEnum::ACIMA_MEDIANA_PORTE->value,
            EventoNarrativoEnum::MUDANCA_RELEVANTE_POSICAO->value,
        ], $this->codigos($eventos));
    }

    public function test_mudanca_para_mediana_nao_e_salto_de_dois_niveis(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'comparativo' => [
                'percentil' => 50.0,
                'percentil_anterior' => 20.0,
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::NA_MEDIANA_PORTE->value], $this->codigos($eventos));
    }
}
