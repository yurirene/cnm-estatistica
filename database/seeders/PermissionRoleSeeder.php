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
                    'digestos',
                    'tutoriais',
                    'acesso-apps',
                    'avisos',
                    'helpdesk'
                ]
            ],
            'diretoria' => [
                'resources' => [
                    'sinodais',
                    'atividades',
                    'minhas-demandas',
                    'tutoriais',
                    'detalhamento',
                    'helpdesk'
                ],
                'permissions' => [
                    'dashboard.datatables.informacao-federacoes',
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.datatables.pesquisas.sinodais',
                    'dashboard.datatables.pesquisas.federacoes',
                    'dashboard.datatables.pesquisas.locais',
                    'dashboard.pesquisas.acompanhar',
                    'dashboard.datatables.formularios-entregues',
                    'dashboard.formularios-sinodal.export',
                    'dashboard.usuarios.resetar-senha'
                ]
            ],
            'sinodal' => [
                'resources' => [
                    'federacoes',
                    'formularios-sinodais',
                    'tutoriais',
                    'apps',
                    'detalhamento',
                    'helpdesk',
                    'diretoria-sinodal'
                ],
                'permissions' => [
                    'dashboard.sinodais.get-ranking',
                    'dashboard.sinodais.update-info',
                    'dashboard.comprovante-aci.index',
                    'dashboard.comprovante-aci.store',
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.responder',
                    'dashboard.datatables.formularios-entregues',
                    'dashboard.formularios-local.export',
                    'dashboard.formularios-federacao.export',
                    'dashboard.avisos.listar',
                    'dashboard.avisos.visualizado'
                ]
            ],
            'federacao' => [
                'resources' => [
                    'umps-locais',
                    'formularios-federacoes',
                    'tutoriais',
                    'helpdesk',
                    'diretoria'
                ],
                'permissions' => [
                    'dashboard.federacoes.update-info',
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.responder',
                    'dashboard.datatables.formularios-entregues',
                    'dashboard.formularios-local.export',
                    'dashboard.formularios-federacao.export',
                    'dashboard.avisos.listar',
                    'dashboard.avisos.visualizado'
                ]
            ],
            'local' => [
                'resources' => [
                    'formularios-locais',
                    'tutoriais',
                    'helpdesk',
                    'diretoria'
                ],
                'permissions' => [
                    'dashboard.pesquisas.index',
                    'dashboard.pesquisas.show',
                    'dashboard.pesquisas.responder',
                    'dashboard.locais.update-info',
                    'dashboard.avisos.listar',
                    'dashboard.avisos.visualizado'
                ]
            ],
            'tesouraria' => [
                'resources' => [
                    'comprovante-aci',
                    'minhas-demandas',
                    'helpdesk'
                ]
            ],
            'executiva' => [
                'resources' => [
                    'demandas',
                    'minhas-demandas',
                    'digestos',
                    'helpdesk'
                ]
            ],
            'secretaria_eventos' => [
                'resources' => [
                    'atividades',
                    'eventos',
                    'minhas-demandas',
                    'helpdesk'
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
                    'minhas-demandas',
                    'helpdesk'
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
                    'minhas-demandas',
                    'helpdesk'
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
                    'minhas-demandas',
                    'helpdesk'
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
                    'minhas-demandas',
                    'helpdesk'
                ]
            ],
            'secretaria_estatistica' => [
                'resources' => [
                    'atividades',
                    'secretaria-estatistica',
                    'minhas-demandas',
                    'avisos',
                    'helpdesk'
                ],
                'permissions' => [
                    'dashboard.datatables.estatistica.formularios-sinodais',
                    'dashboard.datatables.estatistica.formularios-locais',
                    'dashboard.datatables.formularios-entregues'
                ]
            ],
            'secretaria_educacao_crista' => [
                'resources' => [
                    'atividades',
                    'minhas-demandas',
                    'helpdesk'
                ]
            ],
        ];
        DB::table('permission_role')->truncate();
        try {

            foreach ($roles_permissions as $role_slug => $permissions_array) {
                $role = Role::where('slug', $role_slug)->first();
                $permissions = Permission::whereIn('resource', $permissions_array['resources'])
                    ->get()
                    ->pluck('id')
                    ->toArray();
                if (isset($permissions_array['permissions'])) {
                    $array_permission = Permission::whereIn('slug', $permissions_array['permissions'])
                        ->get()
                        ->pluck('id')
                        ->toArray();
                    array_push($permissions, ...$array_permission);
                }
                $role->syncPermissions($permissions);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage(), $permissions);
        }
    }
}
