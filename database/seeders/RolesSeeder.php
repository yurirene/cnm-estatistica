<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Yajra\Acl\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $roles = [
            [
                'name' => 'administrador',
                'slug' => 'administrador',
                'description' => 'Administrador do Sistema',
                'system' => 1
            ],
            [
                'name' => 'diretoria',
                'slug' => 'diretoria',
                'description' => 'Diretoria da CNM',
            ],
            [
                'name' => 'secretarios',
                'slug' => 'secretarios',
                'description' => 'Secretários de Atividades da CNM' 
            ],
            [
                'name' => 'sinodal',
                'slug' => 'sinodal',
                'description' => 'Presidentes das Sinodais'
            ],

            [
                'name' => 'federacao',
                'slug' => 'federacao',
                'description' => 'Presidentes das Federações'
            ],

            [
                'name' => 'local',
                'slug' => 'local',
                'description' => 'Presidentes das Locais'
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($roles as $role) {
                Role::create($role);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }
}
