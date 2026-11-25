<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;

class ConexaoMissionariaCalculador implements CalculadorDePilar
{
    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::ConexaoMissionaria;
        $pontos = $contexto->temConexaoMissionaria ? $pilar->maximo() : 0;

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: $pontos > 0 ? 100 : 0,
            status: $pontos > 0 ? StatusPilar::Ok : StatusPilar::Disponivel,
            rotuloStatus: $pontos > 0 ? 'Concedido' : 'Disponível',
            rodape: 'Realize o projeto "Conexão Missionária" e envie o relatório até 1º de março · 1x por exercício.',
            ctaLabel: 'Saiba como implementar',
            ctaRota: null,
            detalhes: ['concedido' => $contexto->temConexaoMissionaria],
        );
    }
}
