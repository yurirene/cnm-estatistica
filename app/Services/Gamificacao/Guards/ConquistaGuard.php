<?php

namespace App\Services\Gamificacao\Guards;

use App\Exceptions\GamificacaoException;
use App\Models\Gamificacao\Conquista;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\TipoConquista;

class ConquistaGuard
{
    public function validar(
        TipoConquista $tipo,
        NaturezaInstancia $natureza,
        ?string $sinodalId,
        ?string $federacaoId,
        int $ano,
        string $ciclo
    ): void {
        if ($tipo->natureza() !== $natureza) {
            throw new GamificacaoException("A conquista {$tipo->value} não pertence a {$natureza->value}.");
        }

        if ($natureza === NaturezaInstancia::Sinodal && empty($sinodalId)) {
            throw new GamificacaoException('Conquista sinodal exige sinodal_id.');
        }

        if ($natureza === NaturezaInstancia::Federacao && empty($federacaoId)) {
            throw new GamificacaoException('Conquista de federação exige federacao_id.');
        }

        if (! $tipo->unicoNoAno()) {
            return;
        }

        $existe = Conquista::withoutGlobalScopes()
            ->where('tipo', $tipo->value)
            ->where('ano_referencia', $ano)
            ->where('ciclo', $ciclo)
            ->when($sinodalId, fn ($q) => $q->where('sinodal_id', $sinodalId))
            ->when($federacaoId, fn ($q) => $q->where('federacao_id', $federacaoId))
            ->exists();

        if ($existe) {
            throw new GamificacaoException(
                "{$tipo->label()} já foi lançado para esta instância neste ano."
            );
        }
    }
}
