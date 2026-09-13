<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use App\Services\EstatisticaInteligente\Regras\CrescimentoInsightRule;
use App\Services\EstatisticaInteligente\Regras\FaixasNarrativas;
use Tests\TestCase;

class CrescimentoInsightRuleTest extends TestCase
{
    use MontaContexto;

    private CrescimentoInsightRule $regra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regra = new CrescimentoInsightRule(new FaixasNarrativas());
    }

    public function test_nao_emite_evento_sem_dados(): void
    {
        $this->assertSame([], $this->regra->avaliar($this->contexto()));
    }

    public function test_valor_anterior_zero_emite_indeterminado(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'membros_ativos_anterior' => 0,
                'crescimento_ativos_anual' => 20,
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::CRESCIMENTO_INDETERMINADO->value], $this->codigos($eventos));
    }

    /**
     * @dataProvider faixasDeCrescimento
     */
    public function test_classifica_limites_de_variacao(float $variacao, EventoNarrativoEnum $esperado): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'membros_ativos_anterior' => 17,
                'crescimento_ativos_anual' => $variacao,
            ],
        ]));

        $this->assertSame([$esperado->value], $this->codigos($eventos));
    }

    /**
     * @return array<string, array{0: float, 1: EventoNarrativoEnum}>
     */
    public static function faixasDeCrescimento(): array
    {
        return [
            '4.99 estabilidade' => [4.99, EventoNarrativoEnum::ESTABILIDADE],
            '5.00 moderado' => [5.00, EventoNarrativoEnum::CRESCIMENTO_MODERADO],
            '5.01 moderado' => [5.01, EventoNarrativoEnum::CRESCIMENTO_MODERADO],
            '14.99 moderado' => [14.99, EventoNarrativoEnum::CRESCIMENTO_MODERADO],
            '15.00 forte' => [15.00, EventoNarrativoEnum::CRESCIMENTO_FORTE],
            '-4.99 estabilidade' => [-4.99, EventoNarrativoEnum::ESTABILIDADE],
            '-5.00 retracao leve' => [-5.00, EventoNarrativoEnum::RETRACAO_LEVE],
            '-5.01 retracao leve' => [-5.01, EventoNarrativoEnum::RETRACAO_LEVE],
            '-14.99 retracao leve' => [-14.99, EventoNarrativoEnum::RETRACAO_LEVE],
            '-15.00 retracao forte' => [-15.00, EventoNarrativoEnum::RETRACAO_FORTE],
        ];
    }

    public function test_recuperacao_pos_retracao_soma_ao_crescimento(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'membros_ativos_anterior' => 20,
                'crescimento_ativos_anual' => 20.0,
            ],
            'historico' => [
                2025 => ['crescimento_ativos_anual' => -8.0],
            ],
        ]));

        $this->assertSame([
            EventoNarrativoEnum::CRESCIMENTO_FORTE->value,
            EventoNarrativoEnum::RECUPERACAO_POS_RETRACAO->value,
        ], $this->codigos($eventos));
    }

    public function test_nao_emite_recuperacao_se_ano_anterior_nao_retraiu(): void
    {
        $eventos = $this->regra->avaliar($this->contexto([
            'indicadores' => [
                'membros_ativos_anterior' => 20,
                'crescimento_ativos_anual' => 20.0,
            ],
            'historico' => [
                2025 => ['crescimento_ativos_anual' => 2.0],
            ],
        ]));

        $this->assertSame([EventoNarrativoEnum::CRESCIMENTO_FORTE->value], $this->codigos($eventos));
    }
}
