<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use App\Services\Gamificacao\Regras\PontuadorEventos;

class EventosCalculador implements CalculadorDePilar
{
    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::Eventos;
        $pontos = (new PontuadorEventos())->total($contexto->pontosEventosPorTipo, $pilar->maximo());
        $semDados = $pontos === 0;
        $pmfOficial = GamificacaoConfiguracaoService::getInt('conquistas.pmf_oficial.pontos', 5);
        $pmfParceria = GamificacaoConfiguracaoService::getInt('conquistas.pmf_parceria.pontos', 5);
        $djp = GamificacaoConfiguracaoService::getInt('conquistas.djp.pontos', 5);
        $esporadico = GamificacaoConfiguracaoService::getInt('conquistas.esporadico.pontos', 2);

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: (int) round(($pontos / max(1, $pilar->maximo())) * 100),
            status: $pontos > 0 ? StatusPilar::Ok : StatusPilar::Pendente,
            rotuloStatus: $pontos > 0 ? 'Concluído' : 'Sem dados',
            rodape: "PMF Oficial +{$pmfOficial} · PMF Parceria +{$pmfParceria} · DJP +{$djp} · Esporádicos +{$esporadico}/evento · teto {$pilar->maximo()} pts.",
            ctaLabel: $semDados ? null : null,
            ctaRota: null,
            detalhes: ['por_tipo' => $contexto->pontosEventosPorTipo],
        );
    }
}
