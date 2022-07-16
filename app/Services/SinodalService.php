<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\FormularioSinodal;
use App\Models\Sinodal;
use App\Models\User;
use Carbon\Carbon;
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


            if ($request->status == 'A' && $request->has('email_usuario')) {
                $usuario = UserService::usuarioVinculado($request, $sinodal, 'sinodal', 'sinodais');
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

            if ($request->status == 'A' && $request->has('email_usuario')) {
                $usuario = UserService::usuarioVinculado($request, $sinodal, 'sinodal', 'sinodais');
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


    public static function updateInfo(Sinodal $sinodal, Request $request)
    {
        DB::beginTransaction();
        try {
            $sinodal->update([
                'nome' => $request->nome,
                'sinodo' => $request->sinodo,
                'data_organizacao' => Carbon::createFromFormat('d/m/Y', $request->data_organizacao)->format('Y-m-d'),
                'midias_sociais' => $request->midias_sociais
            ]);
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
    public static function getEstados()
    {
        try {
            $usuario = User::find(Auth::id());
            $regioes = Estado::whereIn('regiao_id', $usuario->regioes->pluck('id'))
                ->get()
                ->pluck('nome', 'id');
            return $regioes;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }
    }

    public static function getTotalizadores()
    {
        try {
            $formulario = FormularioSinodal::where('sinodal_id', Auth::user()->sinodais->first()->id)->where('ano_referencia', date('Y'))->first();
            if (!$formulario) {
                return [
                    'total_umps' => 0,
                    'total_federacoes' => 0,
                    'total_socios' => 0,
                ];
            }
            return [
                'total_umps' => $formulario->estrutura['ump_organizada'] ?? 0,
                'total_federacoes' => $formulario->estrutura['federacao_organizada'] ?? 0,
                'total_socios' => intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores'])
            ];
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw $th;
        }
    }

    public static function getInfo()
    {
        try {
            return Auth::user()->sinodais->first();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw $th;
        }
    }

    public static function delete(Sinodal $sinodal)
    {
        try {
            $sinodal->delete();
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