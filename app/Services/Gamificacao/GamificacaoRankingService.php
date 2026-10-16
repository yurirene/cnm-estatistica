<?php

namespace App\Services\Gamificacao;

use App\Models\Gamificacao\Placar;
use App\Services\Gamificacao\Enums\Liga;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Guards\EscritorPlacar;

class GamificacaoRankingService
{
    public function recalcular(NaturezaInstancia $natureza, Liga $liga, string $ciclo, int $ano): void
    {
        EscritorPlacar::executar(function () use ($natureza, $liga, $ciclo, $ano) {
            $query = Placar::withoutGlobalScopes()
                ->where('ciclo', $ciclo)
                ->where('ano_referencia', $ano)
                ->where('liga', $liga->value);

            if ($natureza === NaturezaInstancia::Sinodal) {
                $query->whereNotNull('sinodal_id');
            } else {
                $query->whereNotNull('federacao_id');
            }

            $placares = $query->orderByDesc('pontos_ano')
                ->orderByDesc('pontos_ciclo')
                ->orderBy('id')
                ->get();

            $total = $placares->count();
            $posicao = 1;
            foreach ($placares as $placar) {
                $placar->forceFill([
                    'posicao_liga' => $posicao,
                    'total_na_liga' => $total,
                ])->save();
                $posicao++;
            }
        });
    }
}
