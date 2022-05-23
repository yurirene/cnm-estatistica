<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FederacaoService
{

    public static function store(Request $request)
    {
        try {
            $regiao = Sinodal::find($request->sinodal_id)->regiao_id;
            Federacao::create([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Salvar");
            
        }
    }

    public static function update(Federacao $sinodal, Request $request)
    {
        try {
            $regiao = Sinodal::find($request->sinodal_id)->regiao_id;
            $sinodal->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Atualizar");
            
        }
    }

    public static function getEstados()
    {
        $usuario = User::find(Auth::id());
        $regioes = Estado::whereIn('regiao_id', $usuario->regioes->pluck('id'))
            ->get()
            ->pluck('nome', 'id');
        return $regioes;
    }

    public static function getSinodal()
    {
        $usuario = User::find(Auth::id());
        $regioes = Sinodal::whereIn('regiao_id', $usuario->regioes->pluck('id'))
            ->get()
            ->pluck('nome', 'id');
        return $regioes;
    }

}