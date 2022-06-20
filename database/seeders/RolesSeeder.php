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
                'name'                 => 'administrador',
                'slug'                 => 'administrador',
                'description'          => 'Administrador do Sistema',
                'system'               => 1
            ],
            [
                'name'                 => 'diretoria',
                'slug'                 => 'diretoria',
                'description'          => 'Diretoria da CNM',
            ],


            [
                'name'                 => 'tesouraria',
                'slug'                 => 'tesouraria',
                'description'          => 'Tesouraria da CNM',
            ],

            [
                'name'                 => 'secretaria_eventos',
                'slug'                 => 'secretaria_eventos',
                'description'          => 'Secretaria de Eventos' 
            ],

            [
                'name'                 => 'secretaria_evangelismo',
                'slug'                 => 'secretaria_evangelismo',
                'description'          => 'Secretaria de Evangelismo/Missões' 
            ],

            [
                'name'                 => 'secretaria_responsabilidade',
                'slug'                 => 'secretaria_responsabilidade',
                'description'          => 'Secretaria de Responsabilidade Social' 
            ],



            
            [
                'name'                 => 'sinodal',
                'slug'                 => 'sinodal',
                'description'          => 'Presidentes das Sinodais'
            ],

            [
                'name'                 => 'federacao',
                'slug'                 => 'federacao',
                'description'          => 'Presidentes das Federações'
            ],

            [
                'name'                 => 'local',
                'slug'                 => 'local',
                'description'          => 'Presidentes das Locais'
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($roles as $role) {
                Role::updateOrCreate([
                    'slug' => $role['slug']
                ],
                $role);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }
}
