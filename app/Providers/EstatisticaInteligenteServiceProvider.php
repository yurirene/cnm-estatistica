<?php

namespace App\Providers;

use App\Services\EstatisticaInteligente\Calculo\AnomaliaDetector;
use App\Services\EstatisticaInteligente\Calculo\PorteClassifier;
use App\Services\EstatisticaInteligente\Calculo\QualidadeCalculator;
use App\Services\EstatisticaInteligente\Catalogo\CatalogoNarrativo;
use App\Services\EstatisticaInteligente\Catalogo\PerguntaEstrategicaRegistry;
use App\Services\EstatisticaInteligente\Catalogo\TemplateRendererService;
use App\Services\EstatisticaInteligente\Contratos\AnaliseIaInterface;
use App\Services\EstatisticaInteligente\Contratos\CatalogoNarrativoInterface;
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
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class EstatisticaInteligenteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FaixasNarrativas::class, static fn () => FaixasNarrativas::fromConfig());
        $this->app->singleton(PorteClassifier::class, static fn () => PorteClassifier::fromConfig());
        $this->app->singleton(QualidadeCalculator::class, static fn () => QualidadeCalculator::fromConfig());
        $this->app->singleton(AnomaliaDetector::class, static fn () => AnomaliaDetector::fromConfig());
        $this->app->singleton(CatalogoNarrativoInterface::class, static fn () => CatalogoNarrativo::fromConfig());
        $this->app->singleton(TemplateRendererService::class);
        $this->app->singleton(PerguntaEstrategicaRegistry::class, static function ($app) {
            return PerguntaEstrategicaRegistry::fromConfig($app->make(TemplateRendererService::class));
        });

        $this->app->singleton(InsightRuleRegistry::class, static function ($app) {
            return new InsightRuleRegistry([
                $app->make(CrescimentoInsightRule::class),
                $app->make(DemografiaInsightRule::class),
                $app->make(AtividadesInsightRule::class),
                $app->make(ComparativoInsightRule::class),
                $app->make(AnomaliaInsightRule::class),
                $app->make(QualidadeInsightRule::class),
                $app->make(TendenciaInsightRule::class),
            ]);
        });

        $this->app->bind(AnaliseIaInterface::class, function ($app) {
            $provider = (string) config('estatistica.ai_provider', 'template');

            if ($provider !== 'template') {
                throw new InvalidArgumentException("Provedor de análise '{$provider}' não suportado nesta versão.");
            }

            return $app->make(TemplateAnaliseService::class);
        });
    }
}
