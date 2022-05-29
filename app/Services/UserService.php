<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Perfil;
use App\Models\Regiao;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as FacadesRequest;

class UserService
{

    public const HIERARQUIA = [
        1 => [2],
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
            $usuario = User::updateOrCreate([
                'email' => $request->email,
            ],[
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('123')
            ]);
            if ($request->filled('regiao_id')) {
                $usuario->regioes()->sync($request->regiao_id);
            }

            if ($request->filled('perfil_id')) {
                $usuario->perfis()->attach($request->perfil_id);
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
                $perfis = $usuario->perfis->pluck('id')->toArray();
                $perfil_id = $request->perfil_id;

                if (!in_array($perfil_id, $perfis)) {
                    array_push($perfis, $perfil_id);
                    $usuario->perfis()->sync(array_unique($perfil_id));
                }
            }
            return $usuario;
        } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getLine(), $th->getFile());
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
            
            $perfil = Perfil::where('nome', $perfil)->first();

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