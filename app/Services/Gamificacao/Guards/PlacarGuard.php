<?php

namespace App\Services\Gamificacao\Guards;

use App\Exceptions\GamificacaoException;
use App\Models\Gamificacao\Placar;
use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\Gamificacao\Regras\ValoresLegaisPilar;

class PlacarGuard
{
    public function __construct(
        private readonly ValoresLegaisPilar $valoresLegais,
        private readonly CalendarioGamificacao $calendario,
    ) {
    }

    public function assertInstanciaValida(ContextoInstancia $contexto): void
    {
        $sinodal = $contexto->sinodalId !== null && $contexto->sinodalId !== '';
        $federacao = $contexto->federacaoId !== null && $contexto->federacaoId !== '';

        if ($sinodal === $federacao) {
            throw new GamificacaoException('O placar precisa pertencer a exatamente uma instância.');
        }

        if ($contexto->natureza === NaturezaInstancia::Sinodal && ! $sinodal) {
            throw new GamificacaoException('Placar sinodal sem sinodal_id.');
        }

        if ($contexto->natureza === NaturezaInstancia::Federacao && ! $federacao) {
            throw new GamificacaoException('Placar de federação sem federacao_id.');
        }
    }

    public function assertAnoAberto(Placar $placar, bool $forcar = false): void
    {
        if ($forcar) {
            return;
        }

        if ($placar->estaFechado()) {
            throw new GamificacaoException('O ano da gamificação está fechado e não pode ser alterado.');
        }
    }

    public function assertPilarDaNatureza(NaturezaInstancia $natureza, Pilar $pilar): void
    {
        if ($pilar->exclusivoSinodal() && $natureza !== NaturezaInstancia::Sinodal) {
            throw new GamificacaoException("O pilar {$pilar->value} é exclusivo de sinodal.");
        }

        if ($pilar->exclusivoFederacao() && $natureza !== NaturezaInstancia::Federacao) {
            throw new GamificacaoException("O pilar {$pilar->value} é exclusivo de federação.");
        }
    }

    public function sanitizarPontos(ResultadoPilar $resultado): int
    {
        $pontos = min($resultado->pontosMaximo, max(0, $resultado->pontos));

        return $this->valoresLegais->validar($resultado->pilar, $pontos);
    }

    public function deveFecharAno(int $anoReferencia): bool
    {
        return $this->calendario->anoFechado($anoReferencia);
    }
}
