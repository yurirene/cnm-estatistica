<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Yajra\Acl\Models\Permission;
use Yajra\Acl\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    public function run()
    {
        $roles_permissions = [
            'administrador' => [
                'resources' => [
                    'usuario',
                    'sinodais',
                    'federacoes',
                    'umps_locais',
                    'atividades',
                    'formularios_umps',
                    'datatables-ajax'
                ]
            ],
            'diretoria' => [
                'resources' => [
                    'sinodais',
                    'atividades'
                ],
                'permissions' => [
                    'dashboard.datatables.informacao-federacoes'
                ]
            ],
            'sinodal' => [
                'resources' => [
                    'federacoes',
                    'formularios_sin'
                ],
                'permissions' => [
                    'dashboard.sinodais.update-info',
                    'dashboard.comprovante-aci.index',
                    'dashboard.comprovante-aci.store'
                ]
            ],
            'federacao' => [
                'resources' => [
                    'umps_locais',
                    'formularios_fed'
                ],
                'permissions' => [
                    'dashboard.federacoes.update-info'
                ]
            ],
            'local' => [
                'resources' => [
                    'formularios_ump'
                ],
            ],
            'tesouraria' => [
                'resources' => [
                    'comprovante-aci'
                ]
            ],
            'secretaria_eventos' => [
                'resources' => [
                    'atividades',
                    'eventos',
                    'formulario-sec'
                ],
            ],
            'secretaria_evangelismo' => [
                'resources' => [
                    'atividades',
                    'formulario-sec'
                ],
            ],
        ];
        DB::table('permission_role')->truncate();
        try {
                
            foreach ($roles_permissions as $role_slug => $permissions_array) {
                $role = Role::where('slug', $role_slug)->first();
                $permissions = Permission::whereIn('resource', $permissions_array['resources'])->get()->pluck('id')->toArray();
                if (isset($permissions_array['permissions'])) {
                    $array_permission = Permission::whereIn('slug', $permissions_array['permissions'])->get()->pluck('id')->toArray();
                    array_push($permissions, ...$array_permission);
                }
                $role->syncPermissions($permissions);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}