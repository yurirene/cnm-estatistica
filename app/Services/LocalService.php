<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Local;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocalService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $federacao = Federacao::find($request->federacao_id);
            $local = Local::create([
                'nome' => $request->nome,
                'estado_id' => $federacao->estado_id,
                'federacao_id' => $federacao->id,
                'sinodal_id' => $federacao->sinodal_id,
                'regiao_id' => $federacao->regiao_id,
                'status' => $request->status == 'A' ? true : false
            ]);
             
            $usuario = UserService::usuarioVinculado($request, $local, 'local', 'locais');
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

    public static function update(Local $local, Request $request)
    {
        DB::beginTransaction();
        try {
            $regiao = Estado::find($request->estado_id)->regiao_id;
            $local->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'federacao_id' => $request->federacao_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);
             
            $usuario = UserService::usuarioVinculado($request, $local->id, 'local', 'locais');
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

}