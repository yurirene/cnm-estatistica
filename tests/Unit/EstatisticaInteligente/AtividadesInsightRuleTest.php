<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Regras\AtividadesInsightRule;
use App\Services\EstatisticaInteligente\Regras\FaixasNarrativas;
use Tests\TestCase;

class AtividadesInsightRuleTest extends TestCase
{
    use MontaContexto;

    private AtividadesInsightRule $regra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regra = new AtividadesInsightRule(new FaixasNarrativas());
    }

    public function test_nao_emite_evento_sem_dados(): void
    {
        $this->assertSame([], $this->regra->avaliar($this->contexto()));
    }

    public function test_alta_diversidade_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['diversidade_atividades' => 5],
        ]));

        $this->assertSame([EventoNarrativoEnum::ALTA_DIVERSIDADE_ATIVIDADES->value], $this->codigos($eventos));
    }

    public function test_baixa_diversidade_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['diversidade_atividades' => 2],
        ]));

        $this->assertSame([EventoNarrativoEnum::BAIXA_DIVERSIDADE_ATIVIDADES->value], $this->codigos($eventos));
    }

    public function test_diversidade_neutra_nao_emite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['diversidade_atividades' => 3],
        ]));

        $this->assertSame([], $this->codigos($eventos));
    }

    public function test_concentracao_categoria_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['concentracao_categoria_percentual' => 60.0],
        ]));

        $this->assertSame([EventoNarrativoEnum::CONCENTRACAO_CATEGORIA_ATIVIDADE->value], $this->codigos($eventos));
    }

    public function test_concentracao_59_99_nao_emite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['concentracao_categoria_percentual' => 59.99],
        ]));

        $this->assertSame([], $this->codigos($eventos));
    }

    public function test_aumento_atividades_no_limite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'total_atividades' => 21,
            ],
            'historico' => [
                2025 => ['total_atividades' => 20],
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::AUMENTO_ATIVIDADES->value], $this->codigos($eventos));
    }

    public function test_queda_atividades(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'variacao_atividades_anual' => -5.0,
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::QUEDA_ATIVIDADES->value], $this->codigos($eventos));
    }

    public function test_variacao_abaixo_do_limite_nao_emite(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'variacao_atividades_anual' => 4.99,
            ],
        ]));

        $this->assertSame([], $this->codigos($eventos));
    }
}
