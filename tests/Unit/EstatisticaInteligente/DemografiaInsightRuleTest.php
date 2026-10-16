<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Regras\DemografiaInsightRule;
use App\Services\EstatisticaInteligente\Regras\FaixasNarrativas;
use Tests\TestCase;

class DemografiaInsightRuleTest extends TestCase
{
    use MontaContexto;

    private DemografiaInsightRule $regra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regra = new DemografiaInsightRule(new FaixasNarrativas());
    }

    public function test_nao_emite_evento_sem_dados(): void
    {
        $this->assertSame([], $this->regra->avaliar($this->contexto()));
    }

    public function test_renovacao_alta_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['indice_renovacao_geracional' => 60.0],
        ]));

        $this->assertSame([EventoNarrativoEnum::RENOVACAO_ALTA->value], $this->codigos($eventos));
    }

    public function test_renovacao_59_99_e_silencio(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['indice_renovacao_geracional' => 59.99],
        ]));

        $this->assertSame([], $this->codigos($eventos));
    }

    public function test_renovacao_40_e_silencio(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['indice_renovacao_geracional' => 40.0],
        ]));

        $this->assertSame([], $this->codigos($eventos));
    }

    public function test_renovacao_baixa_abaixo_de_40(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['indice_renovacao_geracional' => 39.99],
        ]));

        $this->assertSame([EventoNarrativoEnum::RENOVACAO_BAIXA->value], $this->codigos($eventos));
    }

    public function test_concentracao_faixa_jovem(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'faixa_etaria_predominante' => 'menores_19',
                'percentual_menores_19' => 45.0,
                'percentual_19_23' => 20.0,
                'percentual_24_29' => 20.0,
                'percentual_30_35' => 15.0,
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::CONCENTRACAO_FAIXA_JOVEM->value], $this->codigos($eventos));
    }

    public function test_concentracao_faixa_adulta(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'faixa_etaria_predominante' => '24_29',
                'percentual_menores_19' => 10.0,
                'percentual_19_23' => 15.0,
                'percentual_24_29' => 50.0,
                'percentual_30_35' => 25.0,
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::CONCENTRACAO_FAIXA_ADULTA->value], $this->codigos($eventos));
    }

    public function test_distribuicao_equilibrada_quando_nenhuma_faixa_atinge_40(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'faixa_etaria_predominante' => '19_23',
                'percentual_menores_19' => 25.0,
                'percentual_19_23' => 30.0,
                'percentual_24_29' => 25.0,
                'percentual_30_35' => 20.0,
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::DISTRIBUICAO_EQUILIBRADA->value], $this->codigos($eventos));
    }

    public function test_nao_emite_concentracao_e_equilibrio_juntos(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'indice_renovacao_geracional' => 62.5,
                'faixa_etaria_predominante' => '19_23',
                'percentual_menores_19' => 30.0,
                'percentual_19_23' => 40.0,
                'percentual_24_29' => 20.0,
                'percentual_30_35' => 10.0,
            ],
        ]));

        $codigos = $this->codigos($eventos);
        $this->assertContains(EventoNarrativoEnum::RENOVACAO_ALTA->value, $codigos);
        $this->assertContains(EventoNarrativoEnum::CONCENTRACAO_FAIXA_JOVEM->value, $codigos);
        $this->assertNotContains(EventoNarrativoEnum::DISTRIBUICAO_EQUILIBRADA->value, $codigos);
    }
}
