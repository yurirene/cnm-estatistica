<?php

namespace Database\Seeders;

use App\Models\Permission as ModelsPermission;
use App\Models\Role as ModelsRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                    'formularios-locais',
                    'formulario-complementar',
                    'datatables',
                    'secretaria-estatistica',
                    'secretaria-produtos',
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
                    'tutoriais',
                    'detalhamento',
                    'helpdesk',
                    'comissao-executiva',
                    'pedidos'
                ],
                'permissions' => [
                    'dashboard.datatables.informacao-federacoes',
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
                    'detalhamento',
                    'helpdesk',
                    'diretoria-sinodal',
                    'ce-sinodal',
                    'formulario-complementar-sinodal',
                    'pesquisas',
                    'sites',
                    'tesouraria'
                ],
                'permissions' => [
                    'dashboard.sinodais.get-ranking',
                    'dashboard.sinodais.update-info',
                    'dashboard.comprovante-aci.index',
                    'dashboard.comprovante-aci.store',
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
                    'diretoria-federacao',
                    'diretoria',
                    'formulario-complementar-federacao',
                    'tesouraria'
                ],
                'permissions' => [
                    'dashboard.federacoes.update-info',
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
                    'diretoria-local',
                    'coletor-dados',
                    'tesouraria'
                ],
                'permissions' => [
                    'dashboard.locais.update-info',
                    'dashboard.avisos.listar',
                    'dashboard.avisos.visualizado',
                ]
            ],
            'tesouraria' => [
                'resources' => [
                    'comprovante-aci',
                    'helpdesk'
                ],
                'permissions' => [
                    'dasbhoard.produtos.index',
                    'dashboard.produtos.datatable.produtos',
                    'dashboard.produtos.datatable.estoque',
                    'dashboard.produtos.datatable.consignacao',
                ]
            ],
            'executiva' => [
                'resources' => [
                    'digestos',
                    'helpdesk',
                    'comissao-executiva'
                ]
            ],
            'secreatria_produtos' => [
                'resources' => [
                    'secretaria-produtos',
                    'pedidos',
                    'helpdesk'
                ],
                'permissions' => []
            ],
            'secretaria_estatistica' => [
                'resources' => [
                    'secretaria-estatistica',
                    'avisos',
                    'helpdesk'
                ],
                'permissions' => [
                    'dashboard.datatables.estatistica.formularios-sinodais',
                    'dashboard.datatables.estatistica.formularios-locais',
                    'dashboard.datatables.formularios-entregues'
                ]
            ],
            'secretariado_comum' => [
                'resources' => [
                    'helpdesk'
                ],
                'permissions' => [                ]
            ],
            'presidente' => [
                'resources' => [
                    'helpdesk',
                    'comissao-executiva'
                ],
                'permissions' => [
                    'dashboard.produtos.index',
                    'dashboard.produtos.relatorios',
                    'dashboard.produtos.relatorios.gerar',
                    'dashboard.produtos.datatable.produtos',
                    'dashboard.produtos.datatable.estoque',
                    'dashboard.produtos.datatable.consignacao',
                ]
            ],
            'produtos_vendedor' => [
                'resources' => [],
                'permissions' => [
                    'dashboard.pedidos.index',
                    'dashboard.pedidos.store',
                ]
            ],
            'produtos_caixa' => [
                'resources' => [],
                'permissions' => [
                    'dashboard.pedidos.caixa',
                    'dashboard.pedidos.separar',
                    'dashboard.pedidos.pagar',
                    'dashboard.pedidos.cancelar'
                ]
            ]
        ];
        DB::table('permission_role')->truncate();
        $permissions = [];
        try {

            foreach ($roles_permissions as $role_slug => $permissions_array) {
                $role = ModelsRole::where('slug', $role_slug)->first();
                $permissions = ModelsPermission::whereIn('resource', $permissions_array['resources'])
                    ->get()
                    ->pluck('id')
                    ->toArray();
                if (isset($permissions_array['permissions'])) {
                    $array_permission = ModelsPermission::whereIn('slug', $permissions_array['permissions'])
                        ->get()
                        ->pluck('id')
                        ->toArray();
                    array_push($permissions, ...$array_permission);
                }
                $role->permissions()->sync($permissions);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage(), $permissions);
        }
    }
}
