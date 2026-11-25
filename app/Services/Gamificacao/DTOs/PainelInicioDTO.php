<?php

namespace App\Services\Gamificacao\DTOs;

use App\Services\Gamificacao\Enums\Liga;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;

class PainelInicioDTO
{
    /**
     * @param  array<int, array<string, mixed>>  $pilares
     * @param  array<int, array<string, mixed>>  $segmentos
     * @param  array<int, array<string, mixed>>  $avisosPontos
     * @param  array<int, array<string, mixed>>  $selos
     */
    public function __construct(
        public readonly string $ciclo,
        public readonly int $ano,
        public readonly int $anoGamificacao,
        public readonly string $natureza,
        public readonly string $liga,
        public readonly string $ligaLabel,
        public readonly string $faixaLiga,
        public readonly int $sociosAtivos,
        public readonly int $posicao,
        public readonly int $totalNaLiga,
        public readonly int $pontosAno,
        public readonly int $tetoAno,
        public readonly int $pontosCiclo,
        public readonly int $tetoCiclo,
        public readonly int $descontoProjetado,
        public readonly int $pontosParaProximoDesconto,
        public readonly string $tendenciaRanking,
        public readonly array $segmentos,
        public readonly array $pilares,
        public readonly array $avisosPontos,
        public readonly array $selos,
        public readonly float $percentualEntrega,
        public readonly int $totalFilhos,
        public readonly int $filhosEntregaram,
        public readonly int $metaEntrega,
    ) {
    }

    public static function vazio(string $natureza): self
    {
        $tetoAno = GamificacaoConfiguracaoService::getInt("tetos.{$natureza}", 110);
        $tetoCiclo = GamificacaoConfiguracaoService::getInt("tetos.ciclo_{$natureza}", 380);

        return new self(
            ciclo: (string) GamificacaoConfiguracaoService::get('ciclo', '2026-2030'),
            ano: (int) date('Y'),
            anoGamificacao: 1,
            natureza: $natureza,
            liga: Liga::Conceicao->value,
            ligaLabel: Liga::Conceicao->label(),
            faixaLiga: '',
            sociosAtivos: 0,
            posicao: 0,
            totalNaLiga: 0,
            pontosAno: 0,
            tetoAno: $tetoAno,
            pontosCiclo: 0,
            tetoCiclo: $tetoCiclo,
            descontoProjetado: 0,
            pontosParaProximoDesconto: GamificacaoConfiguracaoService::getInt("descontos.{$natureza}.50"),
            tendenciaRanking: 'A pontuação do ciclo ainda não foi calculada.',
            segmentos: [],
            pilares: [],
            avisosPontos: [],
            selos: [],
            percentualEntrega: 0,
            totalFilhos: 0,
            filhosEntregaram: 0,
            metaEntrega: GamificacaoConfiguracaoService::getInt('pilares.estatistica.minimo_percentual', 80),
        );
    }
}
