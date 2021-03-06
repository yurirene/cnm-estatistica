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
                'status' => $request->status == 'A' ? true : false ,
                'outro_modelo' => $request->has('outro_modelo') ? true : false
            ]);
            
            if ($request->status == 'A' && $request->has('email_usuario')) {
                $usuario = UserService::usuarioVinculado($request, $local, 'local', 'locais');
                if ($request->has('resetar_senha')) {
                    UserService::resetarSenha($usuario);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw new Exception("Erro ao Salvar");
            
        }
    }

    public static function update(Local $local, Request $request)
    {
        DB::beginTransaction();
        try {
            $federacao = Federacao::find($request->federacao_id);
            $local->update([
                'nome' => $request->nome,
                'estado_id' => $federacao->estado_id,
                'federacao_id' => $federacao->id,
                'sinodal_id' => $federacao->sinodal_id,
                'regiao_id' => $federacao->regiao_id,
                'status' => $request->status == 'A' ? true : false ,
                'outro_modelo' => $request->has('outro_modelo') ? true : false
            ]);
             
            if ($request->status == 'A' && $request->has('email_usuario')) {
                $usuario = UserService::usuarioVinculado($request, $local, 'local', 'locais');
                if ($request->has('resetar_senha')) {
                    UserService::resetarSenha($usuario);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw new Exception("Erro ao Atualizar");
            
        }
    }

    public static function delete(Local $local)
    {
        try {
            $local->delete();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw $th;
        }
    }

}