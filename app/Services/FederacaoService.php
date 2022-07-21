<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\Sinodal;
use App\Models\User;
use Carbon\Carbon;
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
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
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

   
    public static function updateInfo(Federacao $federacao, Request $request)
    {
        DB::beginTransaction();
        try {
            $federacao->update([
                'nome' => $request->nome,
                'presbiterio' => $request->presbiterio,
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

    public static function getInfo()
    {
        try {
            return Auth::user()->federacoes->first();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function getTotalizadores()
    {
        try {
            $formulario = FormularioFederacao::where('federacao_id', Auth::user()->federacoes->first()->id)->where('ano_referencia', date('Y'))->first();
            if (!$formulario) {
                return [
                    'total_umps' => 0,
                    'total_socios' => 0,
                ];
            }
            return [
                'total_umps' => $formulario->estrutura['ump_organizada'] ?? 0,
                'total_socios' => intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores'])
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(Federacao $federacao)
    {
        DB::beginTransaction();
        try {
            $federacao->usuario->first()->update([
                'email' => 'apagadoFedEm'.date('dmyhms').'@apagado.com'
            ]);
            $usuario = $federacao->usuario->first();
            $federacao->usuario()->sync([]);
            $usuario->delete();
            $federacao->delete();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw $th;
        }
    }

    public static function getTotalUmpsOrganizadas(Federacao $federacao, FormularioFederacao $formulario = null) : array
    {
        if (!is_null($formulario)) {
            $total = ($formulario->estrutura['ump_organizada'] ?? 0) + ($formulario->estrutura['ump_nao_organizada'] ?? 0);
            return [
                'total' => $total,
                'organizadas' => $formulario->estrutura['ump_organizada'] ?? 0
            ];
        }
        return [
            'total' => $federacao->locais->count(),
            'organizadas' => $federacao->locais->where('status', true)->count()
        ]; 
    }
}