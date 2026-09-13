<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;

class ResgateCalculador implements CalculadorDePilar
{
    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::Resgate;
        $limiteEvangelismo = GamificacaoConfiguracaoService::getInt('pilares.resgate.maximo_percentual_evangelismo', 60);
        $atingiuMinimoEvangelismo = $contexto->percentualEvangelismo >= $limiteEvangelismo;
        $entregaCompleta = $contexto->percentualEstatistica >= 100;

        $pontos = 0;
        $status = StatusPilar::Disponivel;
        $rotulo = 'Disponível';
        $rodape = "1x/ano no último trimestre se o Evangelismo ficar abaixo de {$limiteEvangelismo}% das UMPs. Não acumula com Evangelismo. Exige 100% da entrega estatística.";

        if ($atingiuMinimoEvangelismo) {
            $status = StatusPilar::Indisponivel;
            $rotulo = 'Indisponível';
            $rodape = "Evangelismo atingiu {$limiteEvangelismo}% das UMPs. O resgate não acumula com esse pilar.";
        } elseif (! $contexto->temResgate) {
            $status = StatusPilar::Disponivel;
            $rotulo = 'Disponível';
        } elseif (! $entregaCompleta) {
            $status = StatusPilar::Pendente;
            $rotulo = 'Aguardando 100% da entrega';
            $rodape = 'Programação de resgate lançada. A pontuação só vale depois de 100% das UMPs entregarem o relatório, se o Evangelismo continuar abaixo de '
                . $limiteEvangelismo . '%.';
        } else {
            $pontos = $pilar->maximo();
            $status = StatusPilar::Ok;
            $rotulo = 'Concedido';
            $rodape = 'Resgate concedido: entrega completa e Evangelismo abaixo de ' . $limiteEvangelismo . '%.';
        }

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: $pontos > 0 ? 100 : 0,
            status: $status,
            rotuloStatus: $rotulo,
            rodape: $rodape,
            ctaLabel: null,
            ctaRota: null,
            detalhes: [
                'elegivel' => ! $atingiuMinimoEvangelismo,
                'concedido' => $contexto->temResgate,
                'entrega_completa' => $entregaCompleta,
                'limite_evangelismo' => $limiteEvangelismo,
            ],
        );
    }
}
