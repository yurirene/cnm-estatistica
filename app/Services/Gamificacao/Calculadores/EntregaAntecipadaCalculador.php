<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;

class EntregaAntecipadaCalculador implements CalculadorDePilar
{
    public function __construct(private readonly CalendarioGamificacao $calendario)
    {
    }

    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::EntregaAntecipada;
        $minimo = GamificacaoConfiguracaoService::getInt('pilares.estatistica.minimo_percentual', 80);
        $estatisticaOk = $contexto->percentualEstatistica >= $minimo;
        $limite = $this->calendario->prazoEntregaAntecipada($contexto->ano);
        $noPrazo = $contexto->dataEntregaEstatistica !== null
            && $contexto->dataEntregaEstatistica->lessThanOrEqualTo($limite);
        $pontos = ($estatisticaOk && $noPrazo) ? $pilar->maximo() : 0;

        $status = StatusPilar::Pendente;
        $rotulo = 'Pendente';
        if (! $estatisticaOk) {
            $status = StatusPilar::Indisponivel;
            $rotulo = 'Indisponível';
        } elseif ($pontos > 0) {
            $status = StatusPilar::Ok;
            $rotulo = 'Concluído';
        }

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: $pontos > 0 ? 100 : 0,
            status: $status,
            rotuloStatus: $rotulo,
            rodape: '80% das UMPs com Formulário preenchido até ' . $limite->format('d/m/Y') . '.',
            ctaLabel: 'Enviar formulário',
            ctaRota: 'dashboard.formularios-federacoes.index',
            detalhes: [
                'estatistica_ok' => $estatisticaOk,
                'no_prazo' => $noPrazo,
            ],
        );
    }
}
