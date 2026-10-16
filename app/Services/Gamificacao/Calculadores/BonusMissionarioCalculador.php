<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;

class BonusMissionarioCalculador implements CalculadorDePilar
{
    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::BonusMissionario;
        $pontos = $contexto->temBonusMissionario ? $pilar->maximo() : 0;

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: $pontos > 0 ? 100 : 0,
            status: $pontos > 0 ? StatusPilar::Ok : StatusPilar::Disponivel,
            rotuloStatus: $pontos > 0 ? 'Concedido' : 'Disponível',
            rodape: 'Faça o projeto "Conexão Missionária" · concedido 1x por ano. Fale com a Secretaria de Missões.',
            ctaLabel: 'Saiba como implementar',
            ctaRota: null,
            detalhes: ['concedido' => $contexto->temBonusMissionario],
        );
    }
}
