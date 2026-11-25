<?php

namespace App\Services\Gamificacao\Regras;

use App\Models\Gamificacao\VinculoExercicio;
use App\Models\Local;
use App\Models\TransferenciaUnidade;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JurisdicaoExercicioResolver
{
    /** @var array<int, bool> */
    private array $temSnapshot = [];

    /**
     * Ano em que a alteração administrativa passa a produzir efeitos (art. 41).
     */
    public function anoEfeito(Carbon $dataAto): int
    {
        // Até 30/06: efeitos no próprio exercício; após 30/06: a partir do seguinte (art. 41).
        return $dataAto->month <= 6 ? $dataAto->year : $dataAto->year + 1;
    }

    public function temSnapshot(int $ano): bool
    {
        if (! array_key_exists($ano, $this->temSnapshot)) {
            $this->temSnapshot[$ano] = VinculoExercicio::query()
                ->where('ano_referencia', $ano)
                ->exists();
        }

        return $this->temSnapshot[$ano];
    }

    /** @return Collection<int, Local> */
    public function locaisDaSinodal(string $sinodalId, int $ano): Collection
    {
        if ($this->temSnapshot($ano)) {
            $ids = VinculoExercicio::query()
                ->where('ano_referencia', $ano)
                ->where('sinodal_id', $sinodalId)
                ->pluck('local_id');

            return Local::query()
                ->whereIn('id', $ids)
                ->where('status', true)
                ->get();
        }

        return $this->locaisCandidatosParaSinodal($sinodalId)
            ->filter(function (Local $local) use ($sinodalId, $ano) {
                $vinculo = $this->vinculoDoLocal($local, $ano);

                return $vinculo['sinodal_id'] === $sinodalId;
            })
            ->values();
    }

    /** @return Collection<int, Local> */
    public function locaisDaFederacao(string $federacaoId, int $ano): Collection
    {
        if ($this->temSnapshot($ano)) {
            $ids = VinculoExercicio::query()
                ->where('ano_referencia', $ano)
                ->where('federacao_id', $federacaoId)
                ->pluck('local_id');

            return Local::query()
                ->whereIn('id', $ids)
                ->where('status', true)
                ->get();
        }

        return $this->locaisCandidatosParaFederacao($federacaoId)
            ->filter(function (Local $local) use ($federacaoId, $ano) {
                $vinculo = $this->vinculoDoLocal($local, $ano);

                return $vinculo['federacao_id'] === $federacaoId;
            })
            ->values();
    }

    /**
     * @return array{federacao_id: string|null, sinodal_id: string|null}
     */
    public function vinculoDoLocal(Local $local, int $ano): array
    {
        if ($this->temSnapshot($ano)) {
            $snap = VinculoExercicio::query()
                ->where('ano_referencia', $ano)
                ->where('local_id', $local->id)
                ->first();

            if ($snap) {
                return [
                    'federacao_id' => $snap->federacao_id,
                    'sinodal_id' => $snap->sinodal_id,
                ];
            }
        }

        $transferencias = TransferenciaUnidade::query()
            ->where('local_id', $local->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return $this->aplicarTransferencias(
            $local->federacao_id,
            $local->sinodal_id,
            $ano,
            $transferencias
        );
    }

    /**
     * Gera/atualiza o snapshot de vínculos de um exercício.
     */
    public function gerarSnapshot(int $ano): int
    {
        $this->temSnapshot[$ano] = false;
        $locais = Local::query()->where('status', true)->get();
        $gravados = 0;

        foreach ($locais as $local) {
            $vinculo = $this->vinculoDoLocalSemSnapshot($local, $ano);
            if (! $vinculo['federacao_id'] || ! $vinculo['sinodal_id']) {
                continue;
            }

            VinculoExercicio::updateOrCreate(
                [
                    'ano_referencia' => $ano,
                    'local_id' => $local->id,
                ],
                [
                    'federacao_id' => $vinculo['federacao_id'],
                    'sinodal_id' => $vinculo['sinodal_id'],
                ]
            );
            $gravados++;
        }

        $this->temSnapshot[$ano] = $gravados > 0;

        return $gravados;
    }

    /**
     * @param  iterable<object>  $transferencias  Mais recentes primeiro
     * @return array{federacao_id: string|null, sinodal_id: string|null}
     */
    public function aplicarTransferencias(
        ?string $federacaoAtual,
        ?string $sinodalAtual,
        int $ano,
        iterable $transferencias
    ): array {
        $federacaoId = $federacaoAtual;
        $sinodalId = $sinodalAtual;

        foreach ($transferencias as $transferencia) {
            $raw = $transferencia->created_at ?? null;
            $dataAto = $raw instanceof Carbon ? $raw : Carbon::parse($raw);
            $efeito = $this->anoEfeito($dataAto);

            if ($efeito > $ano) {
                if (! empty($transferencia->federacao_origem_id)) {
                    $federacaoId = $transferencia->federacao_origem_id;
                }
                if (! empty($transferencia->sinodal_origem_id)) {
                    $sinodalId = $transferencia->sinodal_origem_id;
                }
            }
        }

        return [
            'federacao_id' => $federacaoId,
            'sinodal_id' => $sinodalId,
        ];
    }

    /**
     * @return array{federacao_id: string|null, sinodal_id: string|null}
     */
    private function vinculoDoLocalSemSnapshot(Local $local, int $ano): array
    {
        $transferencias = TransferenciaUnidade::query()
            ->where('local_id', $local->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return $this->aplicarTransferencias(
            $local->federacao_id,
            $local->sinodal_id,
            $ano,
            $transferencias
        );
    }

    /** @return Collection<int, Local> */
    private function locaisCandidatosParaSinodal(string $sinodalId): Collection
    {
        $idsTransferidos = TransferenciaUnidade::query()
            ->where(function ($q) use ($sinodalId) {
                $q->where('sinodal_origem_id', $sinodalId)
                    ->orWhere('sinodal_destino_id', $sinodalId);
            })
            ->whereNotNull('local_id')
            ->pluck('local_id');

        return Local::query()
            ->where('status', true)
            ->where(function ($q) use ($sinodalId, $idsTransferidos) {
                $q->where('sinodal_id', $sinodalId)
                    ->orWhereIn('id', $idsTransferidos);
            })
            ->get();
    }

    /** @return Collection<int, Local> */
    private function locaisCandidatosParaFederacao(string $federacaoId): Collection
    {
        $idsTransferidos = TransferenciaUnidade::query()
            ->where(function ($q) use ($federacaoId) {
                $q->where('federacao_origem_id', $federacaoId)
                    ->orWhere('federacao_destino_id', $federacaoId)
                    ->orWhere('federacao_id', $federacaoId);
            })
            ->whereNotNull('local_id')
            ->pluck('local_id');

        return Local::query()
            ->where('status', true)
            ->where(function ($q) use ($federacaoId, $idsTransferidos) {
                $q->where('federacao_id', $federacaoId)
                    ->orWhereIn('id', $idsTransferidos);
            })
            ->get();
    }
}
