<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;

class AciCalculador implements CalculadorDePilar
{
    public function __construct(private readonly CalendarioGamificacao $calendario)
    {
    }

    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::Aci;
        $minimo = GamificacaoConfiguracaoService::getInt('pilares.aci.minimo_percentual', 60);
        $percentualPago = $contexto->aciValorPrevisto > 0
            ? ($contexto->aciValorRepassado / $contexto->aciValorPrevisto) * 100
            : 0;

        $atingiu = $contexto->natureza === NaturezaInstancia::Sinodal
            ? $contexto->aciMetaAtingida
            : ($contexto->aciRepasseInformado && $percentualPago >= $minimo);
        $pontos = $atingiu ? $pilar->maximo() : 0;
        $prazo = $this->calendario->prazoEstatistica($contexto->ano)->format('d/m/Y');
        $rota = $contexto->natureza === NaturezaInstancia::Sinodal
            ? 'dashboard.comprovante-aci.index'
            : 'dashboard.formularios-federacoes.index';

        $partes = [];
        if ($contexto->natureza === NaturezaInstancia::Sinodal) {
            if ($contexto->aciMetaAtingida) {
                $partes[] = 'Meta de ACI confirmada pela tesouraria';
            } elseif ($contexto->aciComprovanteAnexado) {
                $partes[] = 'Comprovante enviado · aguardando a tesouraria confirmar a meta';
            } else {
                $partes[] = 'Comprovante de ACI não anexado';
            }
        }
        $partes[] = sprintf(
            'mínimo %d%% do valor previsto quitado (%.0f%% agora). Prazo %s.',
            $minimo,
            $percentualPago,
            $prazo
        );

        $ctaLabel = null;
        $ctaRota = $rota;
        if ($contexto->natureza === NaturezaInstancia::Sinodal) {
            $ctaLabel = $contexto->aciComprovanteAnexado ? null : 'Anexar comprovante';
            $ctaRota = $contexto->aciComprovanteAnexado ? null : $rota;
        } else {
            $ctaLabel = 'Registrar ACI';
        }

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: (int) min(100, round($percentualPago)),
            status: $atingiu ? StatusPilar::Ok : StatusPilar::Risco,
            rotuloStatus: $atingiu ? 'Concluído' : 'Em risco',
            rodape: implode(' · ', $partes),
            ctaLabel: $ctaLabel,
            ctaRota: $ctaRota,
            detalhes: [
                'percentual_pago' => $percentualPago,
                'valor_repassado' => $contexto->aciValorRepassado,
                'valor_previsto' => $contexto->aciValorPrevisto,
                'comprovante' => $contexto->aciComprovanteAnexado,
                'meta_atingida' => $contexto->aciMetaAtingida,
            ],
        );
    }
}
