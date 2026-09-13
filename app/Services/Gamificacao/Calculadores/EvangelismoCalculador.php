<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;

class EvangelismoCalculador implements CalculadorDePilar
{
    public static function hint(): string
    {
        $faixas = GamificacaoConfiguracaoService::get('pilares.evangelismo.faixas', []);
        $partes = [];
        foreach ([60, 80, 100] as $corte) {
            $pts = (int) ($faixas[$corte] ?? 0);
            $partes[] = ">={$corte}% das UMPs = {$pts} pts";
        }

        return implode(' · ', $partes) . '. Calculado automaticamente pelas programações informadas no formulário da UMP local.';
    }

    public function calcular(ContextoInstancia $contexto): ResultadoPilar
    {
        $pilar = Pilar::Evangelismo;
        $faixas = GamificacaoConfiguracaoService::get('pilares.evangelismo.faixas', []);
        $pontos = 0;
        foreach ([100, 80, 60] as $corte) {
            if ($contexto->percentualEvangelismo >= $corte) {
                $pontos = (int) ($faixas[$corte] ?? 0);
                break;
            }
        }

        $percentual = (int) round($contexto->percentualEvangelismo);
        $semDados = $contexto->umpsTotal === 0 || $contexto->umpsComProgramacao === 0;
        $status = $pontos > 0 ? StatusPilar::Ok : ($semDados ? StatusPilar::Pendente : StatusPilar::Risco);

        return new ResultadoPilar(
            pilar: $pilar,
            pontos: $pontos,
            pontosMaximo: $pilar->maximo(),
            progresso: min(100, $percentual),
            status: $status,
            rotuloStatus: $pontos > 0 ? 'Concluído' : ($semDados ? 'Sem dados' : 'Em risco'),
            rodape: "{$percentual}% das UMPs com programação registrada",
            ctaLabel: null,
            ctaRota: null,
            hint: self::hint(),
            detalhes: [
                'umps_com_programacao' => $contexto->umpsComProgramacao,
                'umps_total' => $contexto->umpsTotal,
                'percentual' => $contexto->percentualEvangelismo,
            ],
        );
    }
}
