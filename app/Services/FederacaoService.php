<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FederacaoService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $regiao = Sinodal::find($request->sinodal_id)->regiao_id;
            $federacao = Federacao::create([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);
             
            $usuario = UserService::usuarioVinculado($request, $federacao, 'federacao', 'federacoes');
            if ($request->has('resetar_senha')) {
                UserService::resetarSenha($usuario);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Salvar");
            
        }
    }

    public static function update(Federacao $federacao, Request $request)
    {
        DB::beginTransaction();
        try {
            $regiao = Sinodal::find($request->sinodal_id)->regiao_id;
            $federacao->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);
             
            $usuario = UserService::usuarioVinculado($request, $federacao, 'federacao', 'federacoes');
            if ($request->has('resetar_senha')) {
                UserService::resetarSenha($usuario);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
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
        $sinodais = Auth::user()->sinodais;
        $regioes = [];
        foreach ($sinodais as $sinodal) {
            $regioes[] = $sinodal->regiao_id;
        }
        $regioes = Estado::whereIn('regiao_id', $regioes)
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