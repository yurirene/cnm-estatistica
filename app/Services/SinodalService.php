<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Sinodal;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SinodalService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $sinodal = Sinodal::create([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'regiao_id' => $request->regiao_id,
                'status' => $request->status == 'A' ? true : false
            ]);
            
            $usuario = UserService::usuarioVinculado($request, $sinodal, 'sinodal', 'sinodais');
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

    public static function update(Sinodal $sinodal, Request $request)
    {
        DB::beginTransaction();
        try {
            $sinodal->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'regiao_id' => $request->regiao_id,
                'status' => $request->status == 'A' ? true : false
            ]);
            $usuario = UserService::usuarioVinculado($request, $sinodal, 'sinodal', 'sinodais');
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
        $usuario = User::find(Auth::id());
        $regioes = Estado::whereIn('regiao_id', $usuario->regioes->pluck('id'))
            ->get()
            ->pluck('nome', 'id');
        return $regioes;
    }

}