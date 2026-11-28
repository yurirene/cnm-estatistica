<?php

namespace Database\Seeders;

use App\Models\Parametro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            User::ROLE_ADMINISTRADOR,
            User::ROLE_DIRETORIA,
            User::ROLE_SINODAL,
            User::ROLE_FEDERACAO,
            User::ROLE_LOCAL,
        ] as $roleName) {
            Role::query()->firstOrCreate(
                ['name' => $roleName],
                [
                    'slug' => $roleName,
                    'description' => $roleName,
                    'system' => $roleName === User::ROLE_ADMINISTRADOR,
                ]
            );
        }

        Parametro::query()->firstOrCreate(
            ['nome' => 'ano_referencia'],
            [
                'descricao' => 'Ano de referência estatístico',
                'valor' => (string) date('Y'),
            ]
        );
    }
}
