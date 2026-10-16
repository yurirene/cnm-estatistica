<?php

namespace App\Services\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Catalogo\PerguntaEstrategicaRegistry;
use App\Services\EstatisticaInteligente\Catalogo\ResumoExecutivoMontador;
use App\Services\EstatisticaInteligente\Catalogo\TemplateRendererService;
use App\Services\EstatisticaInteligente\Contratos\AnaliseIaInterface;
use App\Services\EstatisticaInteligente\DTOs\AnaliseIaResult;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\DTOs\ItemAnaliseDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\SeveridadeInsightEnum;

final class TemplateAnaliseService implements AnaliseIaInterface
{
    public function __construct(
        private readonly InsightRuleRegistry $registry,
        private readonly TemplateRendererService $renderer,
        private readonly ResumoExecutivoMontador $resumo,
        private readonly PerguntaEstrategicaRegistry $perguntas,
    ) {
    }

    public function analisar(string $prompt, array $contexto): AnaliseIaResult
    {
        unset($prompt);

        $dto = ContextoIaDTO::fromArray($contexto);
        $hash = $dto->hash();
        $eventos = $this->registry->avaliar($dto);

        $itensPorEvento = [];
        foreach ($eventos as $evento) {
            $itensPorEvento[$evento->codigo->value] = $this->renderer->item($evento, $hash);
        }

        $destaques = $this->itens($eventos, $itensPorEvento, static fn (EventoNarrativoDTO $evento) => $evento->severidade === SeveridadeInsightEnum::Positivo);

        $pontosAtencao = $this->itens($eventos, $itensPorEvento, static fn (EventoNarrativoDTO $evento) => in_array($evento->severidade, [
            SeveridadeInsightEnum::Atencao,
            SeveridadeInsightEnum::Critico,
        ], true));

        $tendencias = $this->itens($eventos, $itensPorEvento, static fn (EventoNarrativoDTO $evento) => $evento->codigo->categoria() === CategoriaIndicadorEnum::Tendencia);

        $comparacoes = $this->itens($eventos, $itensPorEvento, static fn (EventoNarrativoDTO $evento) => $evento->codigo->categoria() === CategoriaIndicadorEnum::Comparativo);

        return new AnaliseIaResult(
            titulo: "Panorama estatístico {$dto->periodo}",
            resumo: $this->resumo->montar($eventos, $itensPorEvento),
            destaques: $destaques,
            pontosAtencao: $pontosAtencao,
            tendencias: $tendencias,
            comparacoes: $comparacoes,
            perguntasEstrategicas: $this->perguntas->gerar($eventos, $dto),
            modeloIa: (string) config('estatistica.modelo_template', 'template-engine-v1'),
        );
    }

    /**
     * @param  EventoNarrativoDTO[]  $eventos
     * @param  array<string, ItemAnaliseDTO>  $itensPorEvento
     * @param  callable(EventoNarrativoDTO): bool  $filtro
     * @return ItemAnaliseDTO[]
     */
    private function itens(array $eventos, array $itensPorEvento, callable $filtro): array
    {
        $saida = [];

        foreach ($eventos as $evento) {
            if (! $filtro($evento)) {
                continue;
            }

            $item = $itensPorEvento[$evento->codigo->value] ?? null;
            if ($item !== null) {
                $saida[] = $item;
            }
        }

        return $saida;
    }
}
