<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Catalogo\CatalogoNarrativo;
use App\Services\EstatisticaInteligente\Catalogo\PerguntaEstrategicaRegistry;
use App\Services\EstatisticaInteligente\Catalogo\ResumoExecutivoMontador;
use App\Services\EstatisticaInteligente\Catalogo\TemplateRendererService;
use App\Services\EstatisticaInteligente\InsightRuleRegistry;
use App\Services\EstatisticaInteligente\Regras\AnomaliaInsightRule;
use App\Services\EstatisticaInteligente\Regras\AtividadesInsightRule;
use App\Services\EstatisticaInteligente\Regras\ComparativoInsightRule;
use App\Services\EstatisticaInteligente\Regras\CrescimentoInsightRule;
use App\Services\EstatisticaInteligente\Regras\DemografiaInsightRule;
use App\Services\EstatisticaInteligente\Regras\FaixasNarrativas;
use App\Services\EstatisticaInteligente\Regras\QualidadeInsightRule;
use App\Services\EstatisticaInteligente\Regras\TendenciaInsightRule;
use App\Services\EstatisticaInteligente\TemplateAnaliseService;
use Tests\TestCase;

class TemplateAnaliseServiceTest extends TestCase
{
    use MontaContexto;

    public function test_golden_file_panorama_2026(): void
    {
        $resultado = $this->service()->analisar('', $this->panorama2026());
        $atual = $resultado->toArray();
        $fixture = dirname(__DIR__, 2).'/fixtures/estatistica_inteligente/panorama_2026.json';
        $esperado = json_decode((string) file_get_contents($fixture), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('template-engine-v1', $resultado->modeloIa);
        $this->assertSame($esperado, $atual);
    }

    public function test_mesma_entrada_produz_json_identico(): void
    {
        $service = $this->service();
        $contexto = $this->panorama2026();

        $this->assertSame(
            $service->analisar('', $contexto)->toArray(),
            $service->analisar('', $contexto)->toArray()
        );
    }

    public function test_contexto_vazio_nao_quebra(): void
    {
        $resultado = $this->service()->analisar('', []);

        $this->assertSame('Panorama estatístico 0', $resultado->titulo);
        $this->assertSame([], $resultado->destaques);
        $this->assertSame([], $resultado->pontosAtencao);
        $this->assertNotEmpty($resultado->resumo);
    }

    /**
     * @return array<string, mixed>
     */
    private function panorama2026(): array
    {
        return [
            'periodo' => 2026,
            'nivel' => 'local',
            'organizacao_id' => 'ump-demo',
            'indicadores' => [
                'membros_ativos' => 24,
                'membros_ativos_anterior' => 20,
                'crescimento_ativos_anual' => 20.0,
                'indice_renovacao_geracional' => 62.5,
                'total_atividades' => 39,
                'diversidade_atividades' => 5,
                'faixa_etaria_predominante' => 'menores_19',
                'percentual_menores_19' => 40.0,
                'percentual_19_23' => 22.5,
                'percentual_24_29' => 20.0,
                'percentual_30_35' => 17.5,
            ],
            'historico' => [
                2025 => [
                    'crescimento_ativos_anual' => -8.0,
                    'membros_ativos' => 20,
                    'total_atividades' => 30,
                ],
            ],
            'comparativo' => [
                'percentil' => 80,
                'grupo_pares' => 'das organizações de porte semelhante',
            ],
            'qualidade' => ['score' => 90],
            'anomalias' => [
                ['tipo' => 'variacao_atividades'],
            ],
        ];
    }

    private function service(): TemplateAnaliseService
    {
        $faixas = new FaixasNarrativas();
        $renderer = new TemplateRendererService(CatalogoNarrativo::fromConfig());

        return new TemplateAnaliseService(
            new InsightRuleRegistry([
                new CrescimentoInsightRule($faixas),
                new DemografiaInsightRule($faixas),
                new AtividadesInsightRule($faixas),
                new ComparativoInsightRule($faixas),
                new AnomaliaInsightRule(),
                new QualidadeInsightRule($faixas),
                new TendenciaInsightRule($faixas),
            ]),
            $renderer,
            new ResumoExecutivoMontador(),
            PerguntaEstrategicaRegistry::fromConfig($renderer),
        );
    }
}
