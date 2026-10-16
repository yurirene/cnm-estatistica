<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;

class ComissaoExecutivaCalculador implements CalculadorDePilar
{
    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::ComissaoExecutiva;
        $pontos = $contexto->ceConfirmada ? $pilar->maximo() : 0;

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: $pontos > 0 ? 100 : 0,
            status: $pontos > 0 ? StatusPilar::Ok : StatusPilar::Pendente,
            rotuloStatus: $pontos > 0 ? 'Confirmada' : 'A confirmar',
            rodape: $pontos > 0
                ? 'Presença na Reunião Anual da Comissão Executiva confirmada.'
                : 'Presença na Reunião Anual da Comissão Executiva ainda não confirmada.',
            ctaLabel: 'Confirmar presença',
            ctaRota: 'dashboard.ce-sinodal.index',
            detalhes: ['confirmada' => $contexto->ceConfirmada],
        );
    }
}
