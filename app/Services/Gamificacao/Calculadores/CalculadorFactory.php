<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;

class CalculadorFactory
{
    public function __construct(
        private readonly EstatisticaCalculador $estatistica,
        private readonly AciCalculador $aci,
        private readonly EvangelismoCalculador $evangelismo,
        private readonly ComissaoExecutivaCalculador $comissaoExecutiva,
        private readonly ConexaoMissionariaCalculador $conexaoMissionaria,
        private readonly EntregaAntecipadaCalculador $entregaAntecipada,
        private readonly EventosCalculador $eventos,
    ) {
    }

    /** @return CalculadorDePilar[] */
    public function para(NaturezaInstancia $natureza, ?int $ano = null): array
    {
        $inicio = GamificacaoConfiguracaoService::getInt('ciclo_inicio', 2026);
        if ($ano !== null && $ano === $inicio) {
            return [
                $this->estatistica,
                $this->aci,
            ];
        }

        if ($natureza === NaturezaInstancia::Sinodal) {
            return [
                $this->estatistica,
                $this->aci,
                $this->evangelismo,
                $this->comissaoExecutiva,
                $this->conexaoMissionaria,
            ];
        }

        return [
            $this->estatistica,
            $this->aci,
            $this->evangelismo,
            $this->entregaAntecipada,
            $this->eventos,
        ];
    }
}
