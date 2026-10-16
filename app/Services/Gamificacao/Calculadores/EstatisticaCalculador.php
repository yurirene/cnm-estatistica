<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;

class EstatisticaCalculador implements CalculadorDePilar
{
    public function __construct(private readonly CalendarioGamificacao $calendario)
    {
    }

    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::Estatistica;
        $minimo = GamificacaoConfiguracaoService::getInt('pilares.estatistica.minimo_percentual', 80);
        $atingiu = $contexto->percentualEstatistica >= $minimo;
        $pontos = $atingiu ? $pilar->maximo() : 0;
        $prazo = $this->calendario->prazoEstatistica($contexto->ano)->format('d/m/Y');
        $unidade = $contexto->natureza->value === 'sinodal' ? 'federações' : 'UMPs';
        $percentual = round($contexto->percentualEstatistica);
        $rota = $contexto->natureza->value === 'sinodal'
            ? 'dashboard.formularios-sinodais.index'
            : 'dashboard.formularios-federacoes.index';

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: min(100, $percentual),
            status: $atingiu ? StatusPilar::Ok : StatusPilar::Risco,
            rotuloStatus: $atingiu ? 'Concluído' : 'Em risco',
            rodape: "{$contexto->filhosEntregaram} de {$contexto->totalFilhos} {$unidade} reportaram ({$percentual}%) · mínimo {$minimo}% para pontuar. Prazo {$prazo}.",
            ctaLabel: $atingiu ? 'Ver formulário' : 'Ver formulário',
            ctaRota: $rota,
            detalhes: [
                'entregues' => $contexto->filhosEntregaram,
                'total' => $contexto->totalFilhos,
                'percentual' => $contexto->percentualEstatistica,
                'prazo' => $prazo,
            ],
        );
    }
}
