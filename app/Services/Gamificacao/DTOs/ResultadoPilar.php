<?php

namespace App\Services\Gamificacao\DTOs;

use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;

class ResultadoPilar
{
    /**
     * @param  array<string, mixed>  $detalhes
     */
    public function __construct(
        public readonly Pilar $pilar,
        public readonly int $pontos,
        public readonly int $pontosMaximo,
        public readonly int $progresso,
        public readonly StatusPilar $status,
        public readonly string $rotuloStatus,
        public readonly string $rodape,
        public readonly ?string $ctaLabel,
        public readonly ?string $ctaRota,
        public readonly ?string $hint = null,
        public readonly array $detalhes = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'pilar' => $this->pilar->value,
            'label' => $this->pilar->label(),
            'pontos' => $this->pontos,
            'pontos_maximo' => $this->pontosMaximo,
            'progresso' => $this->progresso,
            'status' => $this->status->value,
            'rotulo_status' => $this->rotuloStatus,
            'rodape' => $this->rodape,
            'cta_label' => $this->ctaLabel,
            'cta_rota' => $this->ctaRota,
            'hint' => $this->hint,
            'detalhes' => $this->detalhes,
            'cor' => $this->pilar->cor(),
        ];
    }
}
