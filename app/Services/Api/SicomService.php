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

    public static function getSinodais()
    {
        $sinodais = Sinodal::where('status', true)
            ->get()
            ->map(function($sinodal) {
                return [
                    'id' => $sinodal->id,
                    'nome' => $sinodal->nome,
                    'sigla' => $sinodal->sigla,
                    'regiao' => $sinodal->regiao->nome,
                ];
            });
        return $sinodais;
    }

    public static function getUnidades()
    {
        $unidades = Sinodal::where('status', true)
            ->with('federacoes', function($query) {
                $query->where('status', true);
            })
            ->get()
            ->map(function($unidade) {
                return [
                    'id' => $unidade->id,
                    'nome' => $unidade->nome,
                    'sigla' => $unidade->sigla,
                    'regiao' => $unidade->regiao->nome,
                    'federacoes' => $unidade->federacoes->map(function($federacao) {
                        return [
                            'id' => $federacao->id,
                            'nome' => $federacao->nome,
                            'sigla' => $federacao->sigla,
                            'regiao' => $federacao->regiao->nome,
                        ];
                    })
                ];
            });

        return $unidades;
    }
}