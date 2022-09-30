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
                    'usuarios',
                    'sinodais',
                    'federacoes',
                    'umps-locais',
                    'atividades',
                    'formularios-locais',
                    'pesquisas',
                    'datatables',
                    'secretaria-estatistica',
                    'secretaria-produtos',
                    'demandas',
                    'digestos'
                ]
            ],
            'diretoria' => [
                'resources' => [
                    'sinodais',
                    'atividades',
                    'minhas-demandas'
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
                    'formularios-sinodais'
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
                    'umps-locais',
                    'formularios-federacoes'
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
                    'formularios-locais'
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
                    'comprovante-aci',
                    'minhas-demandas'
                ]
            ],
            'executiva' => [
                'resources' => [
                    'demandas',
                    'minhas-demandas',
                    'digestos'
                ]
            ],
            'secretaria_eventos' => [
                'resources' => [
                    'atividades',
                    'eventos',
                    'minhas-demandas'
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
                    'minhas-demandas'
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
                    'secretaria-produtos',
                    'minhas-demandas'
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
                    'minhas-demandas'
                ],
                'permissions' => [
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.status',
                    'dashboard.pesquisas.relatorio',
                    'dashboard.pesquisas.relatorio.excel'
                ]
            ],
            'secretaria_comunicacao' => [
                'resources' => [
                    'atividades',
                    'minhas-demandas'
                ]
            ],
            'secretaria_estatistica' => [
                'resources' => [
                    'atividades',
                    'secretaria-estatistica',
                    'minhas-demandas'
                ]
            ],
            'secretaria_educacao_crista' => [
                'resources' => [
                    'atividades',
                    'minhas-demandas'
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
            dd($th->getMessage(), $permissions);
        }
    }
}
