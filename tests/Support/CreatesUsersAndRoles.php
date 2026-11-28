<?php

namespace Tests\Support;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Permission;
use App\Models\Regiao;
use App\Models\Role;
use App\Models\Sinodal;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesUsersAndRoles
{
    protected function seedBaseHierarchia(): array
    {
        Parametro::query()->updateOrCreate(
            ['nome' => 'ano_referencia'],
            [
                'descricao' => 'Ano de referência estatístico',
                'valor' => (string) date('Y'),
            ]
        );

        $regiao = Regiao::query()->firstOrCreate(
            ['nome' => 'Região Teste']
        );

        $estado = Estado::query()->firstOrCreate(
            ['sigla' => 'TS'],
            ['nome' => 'Estado Teste', 'regiao_id' => $regiao->id]
        );

        return compact('regiao', 'estado');
    }

    protected function createRole(string $name, array $permissionSlugs = []): Role
    {
        $role = Role::query()->firstOrCreate(
            ['name' => $name],
            [
                'slug' => Str::slug($name),
                'description' => $name,
                'system' => false,
            ]
        );

        if ($permissionSlugs !== []) {
            $permissionIds = [];
            foreach ($permissionSlugs as $slug) {
                $permission = Permission::query()->firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $slug,
                        'resource' => explode('.', $slug)[0] ?? $slug,
                        'system' => false,
                    ]
                );
                $permissionIds[] = $permission->id;
            }
            $role->permissions()->syncWithoutDetaching($permissionIds);
            $role->clearCache();
        }

        return $role->fresh('permissions');
    }

    protected function createSinodal(?Regiao $regiao = null, array $attrs = []): Sinodal
    {
        $base = $this->seedBaseHierarchia();
        $regiao ??= $base['regiao'];

        return Sinodal::query()->create(array_merge([
            'nome' => 'Sinodal Teste ' . Str::random(5),
            'sigla' => 'ST' . strtoupper(Str::random(3)),
            'regiao_id' => $regiao->id,
            'status' => true,
        ], $attrs));
    }

    protected function createFederacao(Sinodal $sinodal, ?Estado $estado = null, array $attrs = []): Federacao
    {
        $base = $this->seedBaseHierarchia();
        $estado ??= $base['estado'];

        return Federacao::query()->create(array_merge([
            'nome' => 'Federação Teste ' . Str::random(5),
            'sigla' => 'FT' . strtoupper(Str::random(3)),
            'estado_id' => $estado->id,
            'regiao_id' => $sinodal->regiao_id,
            'sinodal_id' => $sinodal->id,
            'status' => true,
        ], $attrs));
    }

    protected function createLocal(Federacao $federacao, array $attrs = []): Local
    {
        return Local::query()->create(array_merge([
            'nome' => 'Local Teste ' . Str::random(5),
            'estado_id' => $federacao->estado_id,
            'regiao_id' => $federacao->regiao_id,
            'federacao_id' => $federacao->id,
            'sinodal_id' => $federacao->sinodal_id,
            'status' => true,
        ], $attrs));
    }

    protected function createUserWithRole(
        string $roleName,
        array $userAttrs = [],
        array $permissionSlugs = []
    ): User {
        $role = $this->createRole($roleName, $permissionSlugs);

        return User::query()->create(array_merge([
            'name' => 'Usuário ' . $roleName,
            'email' => Str::slug($roleName) . '+' . Str::random(6) . '@example.test',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'status' => true,
            'admin' => $roleName === User::ROLE_ADMINISTRADOR,
            'must_change_password' => false,
        ], $userAttrs));
    }

    protected function createSinodalUser(array $permissionSlugs = []): array
    {
        $sinodal = $this->createSinodal();
        $user = $this->createUserWithRole(User::ROLE_SINODAL, [
            'sinodal_id' => $sinodal->id,
            'regiao_id' => $sinodal->regiao_id,
        ], $permissionSlugs);

        return compact('sinodal', 'user');
    }

    protected function createLocalUser(array $permissionSlugs = []): array
    {
        $sinodal = $this->createSinodal();
        $federacao = $this->createFederacao($sinodal);
        $local = $this->createLocal($federacao);
        $user = $this->createUserWithRole(User::ROLE_LOCAL, [
            'sinodal_id' => $sinodal->id,
            'federacao_id' => $federacao->id,
            'local_id' => $local->id,
            'regiao_id' => $sinodal->regiao_id,
        ], $permissionSlugs);

        return compact('sinodal', 'federacao', 'local', 'user');
    }
}
