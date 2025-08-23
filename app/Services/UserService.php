<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Regiao;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        if (auth()->user()->admin) {
            return Role::all();
        }

        $role = self::HIERARQUIA[auth()->user()->role_id];

        return Role::where('id', $role)->get();
    }

    public static function getRegioes()
    {
        if (auth()->user()->admin) {
            return Regiao::all();
        }
        return Regiao::where('id', auth()->user()->regiao_id)->get();
    }

    public static function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $usuario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('123'),
                'role_id' => $request->role_id,
            ]);

            $data = [];

            if ($request->filled('regiao_id')) {
                $data['regiao_id'] = $request->regiao_id;
            }

            if ($request->filled('campo')) {
                $campo = $request->campo;
                $data[$campo] = $request->$campo;
            }

            if (!empty($data)) {
                $usuario->update($data);
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
            $data = [
                'name' => $request->name,
                'email' => $request->email
            ];

            if ($request->filled('regiao_id')) {
                $data['regiao_id'] = $request->regiao_id;
            }

            if ($request->filled('role_id')) {
                $data['role_id'] = $request->role_id;
            }

            if ($request->filled('campo')) {
                $campo = $request->campo;
                $data[$campo] = $request->$campo;
            }

            $usuario->update($data);

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

    public static function usuarioVinculado(
        Request $request,
        Model $instancia,
        string $perfil,
        string $campo
    ) : User {
        try {
            $usuario = $instancia->usuario;

            $perfil = Role::where('name', $perfil)->first();

            $new_request = (new Request([
                'name' => $request->nome_usuario,
                'email' => $request->email_usuario,
                $campo => $instancia->id,
                'role_id' => $perfil->id,
                'campo' => $campo
            ]));


            if (!is_null($usuario)) {
                $usuario =  self::update($usuario, $new_request);
            } else {
                $usuario = self::store($new_request);
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

    public static function checkUser(array $request) : array
    {
        $usuario = User::where('email', $request['email'])
            ->when($request['isNovo'] == "true", function($sql) use ($request) {
                return $sql->where('id', '!=', $request['idUsuario']);
            })
            ->get()
            ->isNotEmpty();
            if ($usuario) {
                return [
                    'status' => false,
                    'msg' => 'E-mail em uso por outra UMP'
                ];
            }
            return [
                'status' => true,
                'msg' => 'E-mail disponível'
            ];
    }

    /**
     * Os registros da plataforma utilizam o id da sinodal, federação e local
     * Este método é responsavel por retornar o campo do banco de dados e o id da instância
     *
     * @return array
     */
    public static function getCampoInstanciaDB(): array
    {
        $retorno = [];
        $perfil = auth()->user()->role->name;

        if ($perfil == User::ROLE_SINODAL) {
            $retorno = [
                'campo' => 'sinodal_id',
                'id' => auth()->user()->sinodal_id
            ];
        }

        if ($perfil == User::ROLE_FEDERACAO) {
            $retorno = [
                'campo' => 'federacao_id',
                'id' => auth()->user()->federacao_id
            ];
        }

        if ($perfil == User::ROLE_LOCAL) {
            $retorno = [
                'campo' => 'local_id',
                'id' => auth()->user()->local_id
            ];
        }
        return $retorno;
    }

    /**
     * Retorna a model da instancia do usuário
     *
     * @param User $usuario
     * @return Model|null
     */
    public static function getInstanciaUsuarioLogado(?User $usuario): ?Model
    {
        if (is_null($usuario)) {
            $usuario = auth()->user();
        }

        $instancia = null;
        if ($usuario->role->name == User::ROLE_SINODAL) {
            $instancia = $usuario->sinodal;
        } elseif ($usuario->role->name == User::ROLE_FEDERACAO) {
            $instancia = $usuario->federacao;
        } elseif ($usuario->role->name == User::ROLE_LOCAL) {
            $instancia = $usuario->local;
        }
        return $instancia;
    }

}
