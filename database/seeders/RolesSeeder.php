<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name'                 => 'executiva',
                'slug'                 => 'executiva',
                'description'          => 'Secretaria Executiva',
            ],
            [
                'name'                 => 'tesouraria',
                'slug'                 => 'tesouraria',
                'description'          => 'Tesouraria da CNM',
            ],

            [
                'name'                 => 'secretariado_comum',
                'slug'                 => 'secretariado_comum',
                'description'          => 'Secretariado'
            ],
            [
                'name'                 => 'secreatria_produtos',
                'slug'                 => 'secreatria_produtos',
                'description'          => 'Secretaria de Produtos'
            ],
            [
                'name'                 => 'secretaria_estatistica',
                'slug'                 => 'secretaria_estatistica',
                'description'          => 'Secretaria de Estatística'
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
            [
                'name'                 => 'presidente',
                'slug'                 => 'presidente',
                'description'          => 'Presidente da CNM',
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
