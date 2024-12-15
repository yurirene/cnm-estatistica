<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigracaoDeEstruturaDeUsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $this->migrarRoles();
            $this->migrarLocais();
            $this->migrarFederacoes();
            $this->migrarSinodais();
            $this->migrarRegioes();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage(), $th->getLine(), $th->getFile());
            throw $th;
        }
    }

    private function migrarRoles()
    {
        $rolesUsers = DB::table('role_user')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->whereNull('users.deleted_at')
            ->get(['role_user.role_id', 'role_user.user_id']);

        foreach ($rolesUsers as $roleUser) {
            User::find($roleUser->user_id)
                ->update([
                    'role_id' => $roleUser->role_id
                ]);
        }
    }

    private function migrarLocais()
    {
        $usuariosLocais = DB::table('usuario_local')->get(['local_id', 'user_id']);

        foreach ($usuariosLocais as $usuarioLocal) {
            User::find($usuarioLocal->user_id)
                ->update([
                    'local_id' => $usuarioLocal->local_id
                ]);
        }
    }

    private function migrarFederacoes()
    {
        $usuariosFederacoes = DB::table('usuario_federacao')->get(['federacao_id', 'user_id']);

        foreach ($usuariosFederacoes as $usuarioFederacao) {
            User::find($usuarioFederacao->user_id)
                ->update([
                    'federacao_id' => $usuarioFederacao->federacao_id
                ]);
        }
    }

    private function migrarSinodais()
    {
        $usuariosSinodais = DB::table('usuario_sinodal')->get(['sinodal_id', 'user_id']);

        foreach ($usuariosSinodais as $usuarioSinodal) {
            User::find($usuarioSinodal->user_id)
                ->update([
                    'sinodal_id' => $usuarioSinodal->sinodal_id
                ]);
        }
    }

    private function migrarRegioes()
    {
        $usuariosRegioes = DB::table('usuario_regiao')
            ->where('user_id', '!=', '53d2c1fd-0b2c-48d3-8262-878b00be8fcf')
            ->get(['regiao_id', 'user_id']);

        foreach ($usuariosRegioes as $usuarioRegiao) {
            User::find($usuarioRegiao->user_id)
                ->update([
                    'regiao_id' => $usuarioRegiao->regiao_id
                ]);
        }
    }
}