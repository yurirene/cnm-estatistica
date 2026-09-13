<?php

namespace App\Services\Gamificacao;

use App\Models\Federacao;
use App\Models\Local;
use App\Services\Gamificacao\Enums\OrigemAuditoria;
use App\Services\LogErroService;

class GamificacaoHook
{
    public static function aposFormularioLocal(string $localId): void
    {
        $local = Local::find($localId);
        if (! $local) {
            return;
        }

        if ($local->federacao_id) {
            self::federacao($local->federacao_id);
        }
        if ($local->sinodal_id) {
            self::sinodal($local->sinodal_id);
        } elseif ($local->federacao?->sinodal_id) {
            self::sinodal($local->federacao->sinodal_id);
        }
    }

    public static function aposFormularioFederacao(string $federacaoId): void
    {
        $federacao = Federacao::find($federacaoId);
        self::federacao($federacaoId);
        if ($federacao?->sinodal_id) {
            self::sinodal($federacao->sinodal_id);
        }
    }

    public static function aposFormularioSinodal(string $sinodalId): void
    {
        self::sinodal($sinodalId, OrigemAuditoria::Formulario);
    }

    public static function aposAci(string $sinodalId): void
    {
        self::sinodal($sinodalId, OrigemAuditoria::Aci);
    }

    public static function aposComissaoExecutiva(string $sinodalId): void
    {
        self::sinodal($sinodalId, OrigemAuditoria::Ce);
    }

    private static function sinodal(string $sinodalId, OrigemAuditoria $origem = OrigemAuditoria::Formulario): void
    {
        try {
            app(GamificacaoAtualizacaoService::class)->recalcularSinodal($sinodalId, null, $origem);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
        }
    }

    private static function federacao(string $federacaoId): void
    {
        try {
            app(GamificacaoAtualizacaoService::class)->recalcularFederacao($federacaoId);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
        }
    }
}
