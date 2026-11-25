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
        public readonly int $sociosParaLiga,
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
        public readonly bool $temConexaoMissionaria,
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

    public function percentualAci(): float
    {
        if ($this->aciValorPrevisto <= 0) {
            return 0.0;
        }

        return ($this->aciValorRepassado / $this->aciValorPrevisto) * 100;
    }

    public function temConquista(TipoConquista $tipo): bool
    {
        return match ($tipo) {
            TipoConquista::ConexaoMissionaria => $this->temConexaoMissionaria,
            default => ($this->pontosEventosPorTipo[$tipo->value] ?? 0) > 0,
        };
    }
}
