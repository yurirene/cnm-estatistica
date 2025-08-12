<?php

namespace App\Services\Api;

use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;

class SicomService
{
    public static function getFederacoes($sinodalId)
    {
        $federacoes = Federacao::where('status', true)
            ->where('sinodal_id', $sinodalId)
            ->get()
            ->map(function($federacao) {
                return [
                    'id' => $federacao->id,
                    'nome' => $federacao->nome,
                    'sigla' => $federacao->sigla
                ];
            });

        return $federacoes;
    }

    public static function getUmpsLocais($federacaoId)
    {
        $umpsLocais = Local::where('status', true)
            ->where('federacao_id', $federacaoId)
            ->get()
            ->map(function($umpsLocal) {
                return [
                    'id' => $umpsLocal->id,
                    'nome' => $umpsLocal->nome,
                ];
            });

        return $umpsLocais;
    }

    public static function validarTokenSinodal($sinodalId)
    {
        $sinodal = Sinodal::find($sinodalId);
        
        if (!$sinodal) {
            return false;
        }
        
        return true;
    }

    public static function validarTokenFederacao($federacaoId)
    {
        $federacao = Federacao::find($federacaoId);
        
        if (!$federacao) {
            return false;
        }
        
        return true;
    }

    public static function validarToken($token)
    {
        $isTokenValid = false;

        if (self::validarTokenSinodal($token)) {
            $isTokenValid = true;
        }

        if (self::validarTokenFederacao($token)) {
            $isTokenValid = true;
        }

        return $isTokenValid;
    }
}