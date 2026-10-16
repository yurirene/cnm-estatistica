<?php

namespace App\Services\EstatisticaInteligente;

use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Services\Estatistica\EstatisticaService;
use App\Services\LogErroService;

final class EstatisticaInteligenteHook
{
    public static function aposFormularioLocal(string $localId): void
    {
        $local = Local::query()->find($localId);
        if (! $local) {
            return;
        }

        self::gerar(app(AlvoAnaliseResolver::class)->aposLocal($local));
    }

    public static function aposFormularioFederacao(string $federacaoId): void
    {
        $federacao = Federacao::query()->find($federacaoId);
        if (! $federacao) {
            return;
        }

        self::gerar(app(AlvoAnaliseResolver::class)->aposFederacao($federacao));
    }

    public static function aposFormularioSinodal(string $sinodalId): void
    {
        $sinodal = Sinodal::query()->find($sinodalId);
        if (! $sinodal) {
            return;
        }

        self::gerar(app(AlvoAnaliseResolver::class)->aposSinodal($sinodal));
    }

    /**
     * @param  array<int, array{nivel: \App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum, id: string}>  $alvos
     */
    private static function gerar(array $alvos): void
    {
        try {
            $ano = EstatisticaService::getAnoReferencia();
            $service = app(GerarAnaliseService::class);
            foreach ($alvos as $alvo) {
                $service->gerar($alvo['nivel'], $alvo['id'], $ano);
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
