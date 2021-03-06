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
                    'formularios_umps'
                ]
            ],
            'diretoria' => [
                'resources' => [
                    'sinodais',
                    'atividades'
                ],
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
/*
1	administrador
2	diretoria
3	secretarios
4	sinodal
5	federacao
6	local


1	Listar Usu??rio
2	Salvar Usu??rio
3	Criar Usu??rio
4	Editar Usu??rio
5	Atualizar Usu??rio
6	Deletar Usu??rio

7	Listar Sinodais
8	Visualizar Sinodal
9	Salvar Sinodais
10	Criar Sinodais
11	Editar Sinodais
12	Atualizar Sinodais
13	Deletar Sinodais

14	Listar Federa????es
15	Visualizar Federa????o
16	Salvar Federa????es
17	Criar Federa????es
18	Editar Federa????es
19	Atualizar Federa????es
20	Deletar Federa????es

21	Listar UMPs Locais
22	Visualizar UMP Local
23	Salvar UMPs Locais
24	Criar UMPs Locais
25	Editar UMPs Locais
26	Atualizar UMPs Locais
27	Deletar UMPs Locais

28	Listar Atividades
29	Calend??rio de Atividades
30	Confirmar Participa????o na Atividades
31	Salvar Atividades
32	Criar Atividades
33	Editar Atividades
34	Atualizar Atividades
35	Deletar Atividades

36	Visualizar Formul??rio UMP Local
37	Salvar Formul??rio UMP Local
*/