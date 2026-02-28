<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\SicomService;

class SicomController extends Controller
{
    public function validarToken($token)
    {
        if (!SicomService::validarToken($token)) {
            return response()->json(['message' => 'Token de acesso inválido'], 400);
        }

        return response()->json(['message' => 'Token de acesso válido']);
    }

    public function isSinodal($token)
    {
        if (SicomService::validarTokenSinodal($token)) {
            return response()->json(true);
        }

        return response()->json(false);
    }

    public function getFederacoes($sinodalId)
    {
        if (!SicomService::validarTokenSinodal($sinodalId)) {
            return response()->json(['message' => 'Token de acesso inválido'], 400);
        }

        return response()->json(SicomService::getFederacoes($sinodalId));
    }

    public function getUmpsLocais($federacaoId)
    {
        if (!SicomService::validarTokenFederacao($federacaoId)) {
            return response()->json(['message' => 'Token de acesso inválido'], 400);
        }

        return response()->json(SicomService::getUmpsLocais($federacaoId));
    }

    public function getSinodais()
    {
        return response()->json(SicomService::getSinodais());
    }

    public function getUnidades()
    {
        return response()->json(SicomService::getUnidades());
    }

    public function getDelegadosExecutiva($reuniaoId)
    {
        return response()->json(SicomService::getDelegadosExecutiva($reuniaoId));
    }

    public function getDelegadosCongressoNacional($reuniaoId)
    {
        return response()->json(SicomService::getDelegadosCongressoNacional($reuniaoId));
    }
}
