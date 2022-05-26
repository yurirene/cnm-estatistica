<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Perfil;
use App\Models\Regiao;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as FacadesRequest;

class UserService
{

    public const HIERARQUIA = [
        1 => [3],
        3 => [4],
        4 => [5]
    ];

    public const INSTANCIAS = [
        'sinodal' => 'App\Models\Sinodal',
        'federacao' => 'App\Models\Federacao',
        'local' => 'App\Models\Local',
    ];

    public static function getPerfis()
    {
        if (Auth::user()->admin) {
            return Perfil::all();
        }
        $perfis = array();

        foreach (Auth::user()->perfis as $perfil) {
            $perfis[] = self::HIERARQUIA[$perfil->id];
        }
        
        return Perfil::whereIn('id', $perfis)->get();
    }

    public static function getRegioes()
    {
        if (Auth::user()->admin) {
            return Regiao::all();
        }
        return Regiao::whereIn('id', Auth::user()->regioes->pluck('id'))->get();
    }

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $usuario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('123')
            ]);
            if ($request->filled('regiao_id')) {
                $usuario->regioes()->sync($request->regiao_id);
            }

            if ($request->filled('perfil_id')) {
                $usuario->perfis()->sync($request->perfil_id);
            }

            DB::commit();
            return $usuario;
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

    public static function update(User $usuario, Request $request)
    {
        try {
            $usuario->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            if ($request->filled('regiao_id')) {
                $usuario->regioes()->sync($request->regiao_id);
            }

            if ($request->filled('perfil_id')) {
                $usuario->perfis()->sync($request->perfil_id);
            }
            return $usuario;
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Atualizar");
            
        }
    }
    
    public static function getAdministrados($usuario) : array
    {
        $usuario = User::find($usuario);
        $administrando = [];

        foreach ($usuario->sinodais as $sinodal) {
            $administrando[] = [
                'texto' => $sinodal->sigla,
                'cor' => 'success'
            ];
        }
        foreach ($usuario->federacoes as $federacao) {
            $administrando[] = [
                'texto' => $federacao->sigla,
                'cor' => 'primary'
            ];
        }
        foreach ($usuario->local as $local) {
            $administrando[] = [
                'texto' => $local->nome,
                'cor' => 'info'
            ];
        }
        return $administrando;
    }

    public static function usuarioVinculado(Request $request, string $id, string $cod_instancia) : User
    {
        try {
            $class = self::INSTANCIAS[$cod_instancia];
            $instancia = $class::where('id', $id)->first();
            $usuario = $instancia->usuario->first();
            
            $perfil = Perfil::where('nome', $cod_instancia)->first();

            $new_request = (new Request([
                'name' => $request->nome_usuario,
                'email' => $request->email_usuario,
                'perfil_id' => [$perfil->id]
            ]));


            if (!is_null($usuario)) {
                $usuario =  self::update($usuario, $new_request);
            } else {
                $usuario = self::store($new_request);
            }
            
            if (!in_array($id, $usuario->sinodais->pluck('id')->toArray())) {
                $novo_vinculo = $usuario->sinodais->pluck('id')->toArray();
                $novo_vinculo[] = $id;
                $usuario->sinodais()->sync($novo_vinculo);
            }
            return $usuario;

        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao processar usuário vinculado");
            
        }
    }

    public static function resetarSenha(User $usuario)
    {
        try {
            $usuario->update([
                'password' => Hash::make('123')
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao resetar senha");
            
        }
    }

    public static function queryUser(User $usuario, string $relacionamento) : array
    {
        foreach ($usuario->$relacionamento as $relacao) {
            $administrando[] = $relacao->id;
        }
        return $administrando;
    }

}