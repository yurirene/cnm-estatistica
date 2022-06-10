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
            1 => [
                'resources' => [
                    'usuario',
                    'sinodais',
                    'federacoes',
                    'umps_locais',
                    'atividades',
                    'formularios_umps'
                ]
            ],
            2 => [
                'resources' => [
                    'usuario',
                    'sinodais',
                    'atividades'
                ],
            ],
            3 => [
                'resources' => [
                    'atividades'
                ],
            ],
            4 => [
                'resources' => [
                    'federacoes',
                    'formularios_sin'
                ],
                'permissions' => [
                    1, 4, 5, 50
                ]
            ],
            5 => [
                'resources' => [
                    'umps_locais',
                    'formularios_fed'
                ],
                'permissions' => [
                    1, 4, 5, 51
                ]
            ],
            6 => [
                'resources' => [
                    'formularios_ump'
                ],
            ]
        ];
        DB::beginTransaction();
        DB::table('permission_role')->truncate();
        try {
                
            foreach ($roles_permissions as $role_id => $permissions_array) {
                $role = Role::find($role_id);
                $permissions = Permission::whereIn('resource', $permissions_array['resources'])->get()->pluck('id')->toArray();
                if (isset($permissions_array['permissions'])) {
                    array_push($permissions, ...$permissions_array['permissions']);
                }
                $role->syncPermissions($permissions);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
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


1	Listar Usuário
2	Salvar Usuário
3	Criar Usuário
4	Editar Usuário
5	Atualizar Usuário
6	Deletar Usuário

7	Listar Sinodais
8	Visualizar Sinodal
9	Salvar Sinodais
10	Criar Sinodais
11	Editar Sinodais
12	Atualizar Sinodais
13	Deletar Sinodais

14	Listar Federações
15	Visualizar Federação
16	Salvar Federações
17	Criar Federações
18	Editar Federações
19	Atualizar Federações
20	Deletar Federações

21	Listar UMPs Locais
22	Visualizar UMP Local
23	Salvar UMPs Locais
24	Criar UMPs Locais
25	Editar UMPs Locais
26	Atualizar UMPs Locais
27	Deletar UMPs Locais

28	Listar Atividades
29	Calendário de Atividades
30	Confirmar Participação na Atividades
31	Salvar Atividades
32	Criar Atividades
33	Editar Atividades
34	Atualizar Atividades
35	Deletar Atividades

36	Visualizar Formulário UMP Local
37	Salvar Formulário UMP Local
*/