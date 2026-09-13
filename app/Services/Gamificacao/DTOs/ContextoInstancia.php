<?php

namespace App\Services\Gamificacao\DTOs;

use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\TipoConquista;
use Carbon\Carbon;

class ContextoInstancia
{
    /**
     * @param  array<string, int>  $pontosEventosPorTipo
     */
    public function __construct(
        public readonly NaturezaInstancia $natureza,
        public readonly ?string $sinodalId,
        public readonly ?string $federacaoId,
        public readonly int $ano,
        public readonly string $ciclo,
        public readonly int $sociosAtivos,
        public readonly int $totalFilhos,
        public readonly int $filhosEntregaram,
        public readonly float $percentualEstatistica,
        public readonly bool $aciComprovanteAnexado,
        public readonly bool $aciRepasseInformado,
        public readonly float $aciValorRepassado,
        public readonly float $aciValorPrevisto,
        public readonly int $umpsTotal,
        public readonly int $umpsComProgramacao,
        public readonly float $percentualEvangelismo,
        public readonly bool $ceConfirmada,
        public readonly bool $temBonusMissionario,
        public readonly bool $temResgate,
        public readonly ?Carbon $dataEntregaEstatistica,
        public readonly array $pontosEventosPorTipo,
        public readonly bool $aciMetaAtingida = false,
        public readonly string $instanciaNome = '',
    ) {
    }

    public function instanciaId(): string
    {
        return $this->natureza === NaturezaInstancia::Sinodal
            ? (string) $this->sinodalId
            : (string) $this->federacaoId;
    }

    public function pontosEventos(): int
    {
        return array_sum($this->pontosEventosPorTipo);
    }

    public function temConquista(TipoConquista $tipo): bool
    {
        return match ($tipo) {
            TipoConquista::BonusMissionario => $this->temBonusMissionario,
            TipoConquista::Resgate => $this->temResgate,
            default => ($this->pontosEventosPorTipo[$tipo->value] ?? 0) > 0,
        };
    }
}
