<?php

namespace App\Services\Instancias;

use App\Models\Federacao;
use App\Models\Local;
use App\Services\LogErroService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $usuario = UserService::usuarioVinculado(
                $request,
                $local,
                'local',
                'local_id'
            );
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

            $usuario = UserService::usuarioVinculado($request, $local, 'local', 'locais');
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

    public static function updateInfo(Local $local, Request $request)
    {
        DB::beginTransaction();
        try {
            $dataOrganizacao = null;
            
            if ($request->filled('data_organizacao')) {
                $dataOrganizacao = Carbon::createFromFormat('d/m/Y', $request->data_organizacao)->format('Y-m-d');
            }

            $local->update([
                'nome' => $request->nome,
                'data_organizacao' => $dataOrganizacao,
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

    public static function delete(Local $local)
    {


        DB::beginTransaction();
        try {
            $local->usuario->update([
                'email' => 'apagadoUMPEm'.date('dmyhms').'@apagado.com',
                'local_id' => null
            ]);
            $local->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function getTotalizadores()
    {
        try {
            $local = auth()->user()->local;
            $formulario = $local->relatorios->last();
            if (!$formulario) {
                return [
                    'total_socios' => 'Sem informação',
                ];
            }
            $total_socios = intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores']);
            return [
                'total_socios' => $total_socios
                    . ' <small style="font-size: 9px;">(Retirado do Formulário Estatístico)</small>'
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getInfo()
    {
        try {
            return auth()->user()->local;
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
