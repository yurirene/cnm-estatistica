<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
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
        $federacao = Auth::user()->federacoes->first();
        try {
            $formulario = FormularioFederacao::where('federacao_id', $federacao->id)->where('ano_referencia', date('Y'))->first();
            if (!$formulario) {
                return [
                    'total_umps' => $federacao->locais->count(),
                    'total_socios' => 'Em Breve',
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
            if ($federacao->usuario->first()) {
                $federacao->usuario->first()->update([
                    'email' => 'apagadoFedEm'.date('dmyhms').'@apagado.com'
                ]);
                $usuario = $federacao->usuario->first();
                $federacao->usuario()->sync([]);
                $usuario->delete();
            }

            
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


    public static function getInformacoesFederacaoOrganizacao(Federacao $federacao) : array
    {
        try {
            $formulario = FormularioFederacao::where('federacao_id', $federacao->id)->where('ano_referencia', date('Y'))->first();

            $total_umps_organizada = self::getTotalUmpsOrganizadas($federacao, $formulario);

            $total_umps_organizada = SinodalService::getPorcentagem($total_umps_organizada['total'], $total_umps_organizada['organizadas']);
            $total_igrejas_n_sociedades = SinodalService::getPorcentagem($federacao->locais->count(), $federacao->locais->where('outro_modelo', true)->count());
            
            return [
                'total_umps_organizada' => $total_umps_organizada,
                'total_igrejas_n_sociedades' => $total_igrejas_n_sociedades
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

    public static function getInformacoesLocaisShow(Federacao $federacao) : array
    {
        try {
        
            $locais = $federacao->locais()->orderBy('status', 'desc')->get();
            $info_local = [];
            foreach ($locais as $local) {

                $utlimo_formulario = $local->relatorios->last();

                $total_socios = 0;
                if (!is_null($utlimo_formulario)) {
                    $total_socios = intval($utlimo_formulario->perfil['ativos'] ?? 0) + intval($utlimo_formulario->perfil['cooperadores'] ?? 0); 
                }

                $info_local[] = [
                    'id' => $local->id,
                    'nome' => $local->nome,
                    'status' => $local->status,
                    'numero_socios' => $total_socios,
                ];
            }
            return $info_local;

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