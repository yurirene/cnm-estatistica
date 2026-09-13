<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Regras\FaixasNarrativas;
use App\Services\EstatisticaInteligente\Regras\TendenciaInsightRule;
use Tests\TestCase;

class TendenciaInsightRuleTest extends TestCase
{
    use MontaContexto;

    private TendenciaInsightRule $regra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regra = new TendenciaInsightRule(new FaixasNarrativas());
    }

    public function test_nao_emite_com_menos_de_tres_anos(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['crescimento_ativos_anual' => 10.0],
            'historico' => [
                2025 => ['crescimento_ativos_anual' => 8.0],
            ],
        ]));

        $this->assertSame([], $this->codigos($eventos));
    }

    public function test_alta_consistente(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['crescimento_ativos_anual' => 10.0],
            'historico' => [
                2025 => ['crescimento_ativos_anual' => 8.0],
                2024 => ['crescimento_ativos_anual' => 6.0],
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::TENDENCIA_ALTA_CONSISTENTE->value], $this->codigos($eventos));
    }

    public function test_queda_consistente(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['crescimento_ativos_anual' => -10.0],
            'historico' => [
                2025 => ['crescimento_ativos_anual' => -8.0],
                2024 => ['crescimento_ativos_anual' => -3.0],
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::TENDENCIA_QUEDA_CONSISTENTE->value], $this->codigos($eventos));
    }

    public function test_oscilante(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => ['crescimento_ativos_anual' => 10.0],
            'historico' => [
                2025 => ['crescimento_ativos_anual' => -8.0],
                2024 => ['crescimento_ativos_anual' => 6.0],
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::TENDENCIA_OSCILANTE->value], $this->codigos($eventos));
    }
}
