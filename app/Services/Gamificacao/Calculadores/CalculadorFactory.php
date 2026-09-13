<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\Enums\NaturezaInstancia;

class CalculadorFactory
{
    public function __construct(
        private readonly EstatisticaCalculador $estatistica,
        private readonly AciCalculador $aci,
        private readonly EvangelismoCalculador $evangelismo,
        private readonly ComissaoExecutivaCalculador $comissaoExecutiva,
        private readonly BonusMissionarioCalculador $bonusMissionario,
        private readonly SpeedRunCalculador $speedRun,
        private readonly EventosCalculador $eventos,
        private readonly ResgateCalculador $resgate,
    ) {
    }

    /** @return CalculadorDePilar[] */
    public function para(NaturezaInstancia $natureza): array
    {
        if ($natureza === NaturezaInstancia::Sinodal) {
            return [
                $this->estatistica,
                $this->aci,
                $this->evangelismo,
                $this->comissaoExecutiva,
                $this->bonusMissionario,
            ];
        }

        return [
            $this->estatistica,
            $this->aci,
            $this->evangelismo,
            $this->speedRun,
            $this->eventos,
            $this->resgate,
        ];
    }
}
