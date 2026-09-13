<?php

namespace App\Services\EstatisticaInteligente\Catalogo;

use App\Services\EstatisticaInteligente\Contratos\CatalogoNarrativoInterface;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\DTOs\ItemAnaliseDTO;
use Illuminate\Support\Facades\Log;

final class TemplateRendererService
{
    public function __construct(private readonly CatalogoNarrativoInterface $catalogo)
    {
    }

    public function renderizar(EventoNarrativoDTO $evento, string $contextoHash): string
    {
        $template = $this->catalogo->selecionar($evento->codigo, $contextoHash);

        return $this->interpolar($template, $evento->variaveis);
    }

    public function item(EventoNarrativoDTO $evento, string $contextoHash): ItemAnaliseDTO
    {
        return new ItemAnaliseDTO(
            titulo: $evento->codigo->titulo(),
            descricao: $this->renderizar($evento, $contextoHash),
            tipo: $evento->severidade,
        );
    }

    /**
     * @param  EventoNarrativoDTO[]  $eventos
     * @return ItemAnaliseDTO[]
     */
    public function renderizarTodos(array $eventos, string $contextoHash): array
    {
        return array_map(
            fn (EventoNarrativoDTO $evento): ItemAnaliseDTO => $this->item($evento, $contextoHash),
            $eventos
        );
    }

    /**
     * @param  array<string, mixed>  $variaveis
     */
    public function interpolar(string $template, array $variaveis): string
    {
        foreach ($variaveis as $chave => $valor) {
            $template = str_replace('{'.$chave.'}', $this->formatar($valor), $template);
        }

        if (preg_match('/\{[a-z0-9_]+\}/i', $template)) {
            Log::warning('Placeholder narrativo sem substituição.', ['template' => $template]);
            $template = preg_replace('/\{[a-z0-9_]+\}/i', '', $template) ?? $template;
            $template = preg_replace('/\s+/', ' ', $template) ?? $template;
        }

        return trim($template);
    }

    private function formatar(mixed $valor): string
    {
        if (is_int($valor)) {
            return (string) $valor;
        }

        if (is_float($valor)) {
            if (abs($valor - round($valor)) < 0.00001) {
                return (string) (int) round($valor);
            }

            return number_format($valor, 1, ',', '');
        }

        if (is_bool($valor)) {
            return $valor ? 'sim' : 'não';
        }

        return (string) $valor;
    }
}
