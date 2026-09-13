<?php

namespace App\Services\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Models\Federacao;
use App\Models\Gamificacao\Conquista;
use App\Models\Sinodal;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\OrigemAuditoria;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\Guards\ConquistaGuard;
use App\Services\Gamificacao\Guards\EscritorPlacar;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\LogErroService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GamificacaoConquistaService
{
    public function __construct(
        private readonly ConquistaGuard $guard,
        private readonly CalendarioGamificacao $calendario,
        private readonly GamificacaoAtualizacaoService $atualizacao,
    ) {
    }

    /**
     * @param  string[]  $federacaoIds
     * @param  string[]  $sinodalIds
     */
    public function conceder(
        TipoConquista $tipo,
        array $federacaoIds,
        array $sinodalIds,
        string $referencia,
        int $ano
    ): int {
        $natureza = $tipo->natureza();
        $federacaoIds = array_values(array_unique(array_filter($federacaoIds)));
        $sinodalIds = array_values(array_unique(array_filter($sinodalIds)));

        if ($natureza === NaturezaInstancia::Sinodal) {
            if ($federacaoIds !== []) {
                throw new GamificacaoException('Bônus Missionário é lançado para sinodais, não para federações.');
            }
            $ids = $sinodalIds;
        } else {
            if ($sinodalIds !== []) {
                throw new GamificacaoException('Este evento é lançado para federações, não para sinodais.');
            }
            $ids = $federacaoIds;
        }

        if ($ids === []) {
            throw new GamificacaoException('Selecione ao menos uma instância.');
        }

        $referencia = trim($referencia);
        if ($tipo === TipoConquista::Esporadico && $referencia === '') {
            throw new GamificacaoException('Informe o nome do evento esporádico.');
        }
        if ($referencia === '') {
            $referencia = $tipo->label();
        }

        $ciclo = $this->calendario->ciclo();
        $this->assertUnicoNoAno($tipo, $natureza, $ids, $ano, $ciclo);
        $userId = auth()->id();

        $criadas = EscritorPlacar::executar(function () use ($tipo, $natureza, $ids, $referencia, $ano, $ciclo, $userId) {
            return DB::transaction(function () use ($tipo, $natureza, $ids, $referencia, $ano, $ciclo, $userId) {
                $total = 0;
                foreach ($ids as $id) {
                    $sinodalId = $natureza === NaturezaInstancia::Sinodal ? $id : null;
                    $federacaoId = $natureza === NaturezaInstancia::Federacao ? $id : null;
                    $this->guard->validar($tipo, $natureza, $sinodalId, $federacaoId, $ano, $ciclo);

                    $conquista = new Conquista();
                    $conquista->forceFill([
                        'tipo' => $tipo,
                        'ciclo' => $ciclo,
                        'ano_referencia' => $ano,
                        'sinodal_id' => $sinodalId,
                        'federacao_id' => $federacaoId,
                        'pontos' => $tipo->pontos(),
                        'referencia' => $referencia,
                        'concedido_em' => now(),
                        'concedido_por' => $userId,
                    ]);
                    $conquista->save();
                    $total++;
                }

                return $total;
            });
        });

        foreach ($ids as $id) {
            $this->recalcular($natureza, $id, $ano);
        }

        return $criadas;
    }

    public function revogar(Conquista $conquista): void
    {
        $natureza = $conquista->tipo->natureza();
        $instanciaId = $natureza === NaturezaInstancia::Sinodal
            ? (string) $conquista->sinodal_id
            : (string) $conquista->federacao_id;
        $ano = (int) $conquista->ano_referencia;

        EscritorPlacar::executar(function () use ($conquista) {
            $conquista->delete();
        });

        $this->recalcular($natureza, $instanciaId, $ano);
    }

    public function listar(int $ano): Collection
    {
        return Conquista::withoutGlobalScopes()
            ->with(['federacao.sinodal', 'sinodal', 'concedente'])
            ->where('ano_referencia', $ano)
            ->where('ciclo', $this->calendario->ciclo())
            ->orderByDesc('concedido_em')
            ->orderBy('tipo')
            ->get();
    }

    /**
     * @param  string[]  $ids
     */
    private function assertUnicoNoAno(
        TipoConquista $tipo,
        NaturezaInstancia $natureza,
        array $ids,
        int $ano,
        string $ciclo
    ): void {
        if (! $tipo->unicoNoAno()) {
            return;
        }

        $coluna = $natureza === NaturezaInstancia::Sinodal ? 'sinodal_id' : 'federacao_id';
        $duplicadas = Conquista::withoutGlobalScopes()
            ->where('tipo', $tipo->value)
            ->where('ano_referencia', $ano)
            ->where('ciclo', $ciclo)
            ->whereIn($coluna, $ids)
            ->pluck($coluna)
            ->unique()
            ->values();

        if ($duplicadas->isEmpty()) {
            return;
        }

        $nomes = $natureza === NaturezaInstancia::Sinodal
            ? Sinodal::query()->whereIn('id', $duplicadas)->orderBy('nome')->pluck('nome')
            : Federacao::query()->whereIn('id', $duplicadas)->orderBy('nome')->pluck('nome');

        $lista = $nomes->filter()->implode(', ') ?: $duplicadas->implode(', ');

        throw new GamificacaoException(
            "{$tipo->label()} já foi lançado neste ano para: {$lista}."
        );
    }

    private function recalcular(NaturezaInstancia $natureza, string $instanciaId, int $ano): void
    {
        try {
            if ($natureza === NaturezaInstancia::Sinodal) {
                $this->atualizacao->recalcularSinodal($instanciaId, $ano, OrigemAuditoria::Conquista);
            } else {
                $this->atualizacao->recalcularFederacao($instanciaId, $ano, OrigemAuditoria::Conquista);
            }
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
        }
    }
}
