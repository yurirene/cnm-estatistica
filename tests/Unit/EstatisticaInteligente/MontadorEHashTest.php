<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Models\Estatistica\AnaliseEstatistica;
use App\Services\EstatisticaInteligente\Calculo\AnomaliaDetector;
use App\Services\EstatisticaInteligente\Calculo\IndicadorDerivador;
use App\Services\EstatisticaInteligente\Calculo\PercentilCalculator;
use App\Services\EstatisticaInteligente\Calculo\PorteClassifier;
use App\Services\EstatisticaInteligente\Calculo\QualidadeCalculator;
use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;
use App\Services\EstatisticaInteligente\Enums\StatusAnaliseEnum;
use App\Services\EstatisticaInteligente\GerarAnaliseService;
use App\Services\EstatisticaInteligente\MontadorContextoService;
use Tests\TestCase;

class MontadorEHashTest extends TestCase
{
    public function test_montar_de_dados_compara_ano_anterior_e_percentil(): void
    {
        $dto = $this->montador()->montarDeDados(
            NivelEstatisticoEnum::Local,
            'ump-1',
            2026,
            [
                'perfil' => [
                    'ativos' => 24,
                    'cooperadores' => 0,
                    'menor19' => 10,
                    'de19a23' => 5,
                    'de24a29' => 5,
                    'de30a35' => 4,
                ],
                'programacoes' => [
                    'social' => 8,
                    'evangelistico' => 5,
                    'espiritual' => 17,
                    'recreativo' => 5,
                    'oracao' => 4,
                ],
            ],
            [
                'perfil' => ['ativos' => 20, 'cooperadores' => 0, 'menor19' => 8, 'de19a23' => 4, 'de24a29' => 4, 'de30a35' => 4],
                'programacoes' => ['social' => 6, 'evangelistico' => 4, 'espiritual' => 10, 'recreativo' => 5, 'oracao' => 5],
            ],
            null,
            [10, 20, 24],
            [8, 15, 20],
            'das UMPs da mesma federação',
        );

        $this->assertEquals(20.0, $dto->indicador('crescimento_ativos_anual'));
        $this->assertSame(20, $dto->indicador('membros_ativos_anterior'));
        $this->assertNotNull($dto->percentil());
        $this->assertSame('das UMPs da mesma federação', $dto->comparativo['grupo_pares'] ?? null);
        $this->assertNotEmpty($dto->hash());
    }

    public function test_mesmo_contexto_nao_reprocessa(): void
    {
        $existente = new AnaliseEstatistica();
        $existente->contexto_hash = 'abc';
        $existente->catalogo_version = '1.1';
        $existente->status = StatusAnaliseEnum::Concluida->value;

        $service = $this->app->make(GerarAnaliseService::class);

        $this->assertTrue($service->mesmoContexto($existente, 'abc', '1.1'));
        $this->assertFalse($service->mesmoContexto($existente, 'outro', '1.1'));
        $this->assertFalse($service->mesmoContexto($existente, 'abc', '1.2'));
    }

    public function test_partial_nula_nao_quebra(): void
    {
        $html = view('dashboard.index.partes.analise-estatistica', [
            'analise' => [],
            'variante' => 'card',
        ])->render();

        $this->assertStringContainsString('após a entrega do relatório deste ano', $html);
    }

    private function montador(): MontadorContextoService
    {
        return new MontadorContextoService(
            new IndicadorDerivador(),
            new PercentilCalculator(),
            PorteClassifier::fromConfig(),
            QualidadeCalculator::fromConfig(),
            AnomaliaDetector::fromConfig(),
        );
    }
}
