<?php

namespace App\Services\EstatisticaInteligente\Catalogo;

use App\Services\EstatisticaInteligente\Contratos\CatalogoNarrativoInterface;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use InvalidArgumentException;

final class CatalogoNarrativo implements CatalogoNarrativoInterface
{
    /**
     * @param  array<string, string[]>  $textos
     */
    public function __construct(private readonly array $textos)
    {
    }

    public static function fromConfig(): self
    {
        return new self(config('estatistica_narrativa.textos', []));
    }

    public function textos(EventoNarrativoEnum $evento): array
    {
        $variantes = $this->textos[$evento->value] ?? [];

        return array_values(array_filter($variantes, static fn ($texto) => is_string($texto) && $texto !== ''));
    }

    public function selecionar(EventoNarrativoEnum $evento, string $contextoHash): string
    {
        $variantes = $this->textos($evento);
        if ($variantes === []) {
            throw new InvalidArgumentException("Catálogo sem variantes para o evento {$evento->value}.");
        }

        $indice = ((int) sprintf('%u', crc32($contextoHash.$evento->value))) % count($variantes);

        return $variantes[$indice];
    }
}
