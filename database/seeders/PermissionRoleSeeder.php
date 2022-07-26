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
                    'pesquisas',
                    'datatables-ajax'
                ]
            ],
            'diretoria' => [
                'resources' => [
                    'sinodais',
                    'atividades'
                ],
                'permissions' => [
                    'dashboard.datatables.informacao-federacoes',
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.datatables.pesquisas.sinodais',
                    'dashboard.datatables.pesquisas.federacoes',
                    'dashboard.datatables.pesquisas.locais',
                    'dashboard.pesquisas.acompanhar'
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
                    'dashboard.comprovante-aci.store',
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.responder'
                ]
            ],
            'federacao' => [
                'resources' => [
                    'umps_locais',
                    'formularios_fed'
                ],
                'permissions' => [
                    'dashboard.federacoes.update-info',
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.responder'
                ]
            ],
            'local' => [
                'resources' => [
                    'formularios_ump'
                ],
                'permissions' => [
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.responder',
                    'dashboard.locais.update-info'
                ]
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
                ],
                'permissions' => [
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.status',
                    'dashboard.pesquisas.relatorio',
                    'dashboard.pesquisas.relatorio.excel'
                ]
            ],
            'secretaria_evangelismo' => [
                'resources' => [
                    'atividades',
                ],
                'permissions' => [
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.status',
                    'dashboard.pesquisas.relatorio',
                    'dashboard.pesquisas.relatorio.excel'
                ]
            ],
            'secreatria_produtos' => [
                'resources' => [
                    'atividades',
                ],
                'permissions' => [
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.status',
                    'dashboard.pesquisas.relatorio',
                    'dashboard.pesquisas.relatorio.excel'
                ]
            ],
            'secretaria_responsabilidade' => [
                'resources' => [
                    'atividades',
                ],
                'permissions' => [
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.status',
                    'dashboard.pesquisas.relatorio',
                    'dashboard.pesquisas.relatorio.excel'
                ]
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