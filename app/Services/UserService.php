<?php

namespace App\Services;

use Yajra\Acl\Models\Role;
use App\Models\Regiao;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{

    public const HIERARQUIA = [
        1 => [2, 3],
        2 => [4],
        4 => [5],
        5 => [6]
    ];

    public const INSTANCIAS = [
        'sinodal' => 'App\Models\Sinodal',
        'federacao' => 'App\Models\Federacao',
        'local' => 'App\Models\Local',
    ];

    public static function getRoles()
    {
        if (Auth::user()->admin) {
            return Role::all();
        }
        $roles = array();

        foreach (Auth::user()->roles as $role) {
            $roles[] = self::HIERARQUIA[$role->id];
        }
        
        return Role::whereIn('id', $roles)->get();
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
                $roles = $usuario->roles->pluck('id')->toArray();
                $perfil_id = $request->perfil_id;

                if (!in_array($perfil_id, $roles)) {
                    array_push($roles, $perfil_id);
                    $usuario->syncRoles(array_unique($perfil_id));
                }
            }

            DB::commit();
            return $usuario;
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
                $roles = $usuario->roles->pluck('id')->toArray();
                $perfil_id = $request->perfil_id;

                if (!in_array($perfil_id, $roles)) {
                    $perfis = array_merge($roles, $perfil_id);
                    $usuario->syncRoles(array_unique($perfis));
                }
            }
            return $usuario;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
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
        foreach ($usuario->locais as $local) {
            $administrando[] = [
                'texto' => $local->nome,
                'cor' => 'info'
            ];
        }
        return $administrando;
    }

    public static function usuarioVinculado(Request $request, Model $instancia, string $perfil, string $relacao) : User
    {
        try {
            $usuario = $instancia->usuario->first();
            
            $perfil = Role::where('name', $perfil)->first();

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
            
            if (!in_array($instancia->id, $usuario->$relacao->pluck('id')->toArray())) {
                $novo_vinculo = $usuario->$relacao->pluck('id')->toArray();
                $novo_vinculo[] = $instancia->id;
                $usuario->$relacao()->sync($novo_vinculo);
            }
            return $usuario;

        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
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
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
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