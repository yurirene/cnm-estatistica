<?php

namespace App\Services\EstatisticaInteligente\Catalogo;

use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class PerguntaEstrategicaRegistry
{
    /**
     * @param  array<int, array{eventos: string[], texto: string, ordem?: int}>  $perguntas
     */
    public function __construct(
        private readonly array $perguntas,
        private readonly TemplateRendererService $renderer,
    ) {
    }

    public static function fromConfig(TemplateRendererService $renderer): self
    {
        return new self(config('estatistica_narrativa.perguntas', []), $renderer);
    }

    /**
     * @param  EventoNarrativoDTO[]  $eventos
     * @return string[]
     */
    public function gerar(array $eventos, ContextoIaDTO $contexto): array
    {
        $codigos = array_map(static fn (EventoNarrativoDTO $evento) => $evento->codigo->value, $eventos);
        $porCodigo = [];
        foreach ($eventos as $evento) {
            $porCodigo[$evento->codigo->value] = $evento;
        }

        $perguntas = $this->perguntas;
        usort($perguntas, static fn (array $a, array $b) => ($a['ordem'] ?? 0) <=> ($b['ordem'] ?? 0));

        $saida = [];
        foreach ($perguntas as $pergunta) {
            $gatilhos = $pergunta['eventos'] ?? [];
            $disparado = null;
            foreach ($gatilhos as $gatilho) {
                if (in_array($gatilho, $codigos, true)) {
                    $disparado = $porCodigo[$gatilho];
                    break;
                }
            }

            if ($disparado === null) {
                continue;
            }

            $variaveis = array_merge($disparado->variaveis, [
                'ano_atual' => $contexto->periodo,
                'ano_anterior' => $contexto->periodo > 0 ? $contexto->periodo - 1 : $contexto->periodo,
                'evento_textual' => EventoNarrativoEnum::from($disparado->codigo->value)->eventoTextual(),
            ]);

            $saida[] = $this->renderer->interpolar($pergunta['texto'], $variaveis);
        }

        return array_values(array_unique($saida));
    }
}
