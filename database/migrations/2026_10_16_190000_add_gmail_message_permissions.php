<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissoes = [
            [
                'name' => 'Módulo Gmail - Visualizar - S',
                'slug' => 'dashboard.gmail.show',
                'resource' => 'gmail',
            ],
            [
                'name' => 'Módulo Gmail - anexo',
                'slug' => 'dashboard.gmail.anexo',
                'resource' => 'gmail',
            ],
        ];

        $ids = [];

        foreach ($permissoes as $dados) {
            $permissao = Permission::updateOrCreate(
                ['slug' => $dados['slug']],
                $dados
            );
            $ids[] = $permissao->id;
        }

        $agora = now();
        Role::query()
            ->whereIn('slug', [
                'executiva',
                'secretariado_comum',
                'diretoria',
                'presidente',
                'tesouraria',
            ])
            ->get()
            ->each(function (Role $role) use ($ids, $agora) {
                foreach ($ids as $permissionId) {
                    $existe = DB::table('permission_role')
                        ->where('role_id', $role->id)
                        ->where('permission_id', $permissionId)
                        ->exists();

                    if (! $existe) {
                        DB::table('permission_role')->insert([
                            'role_id' => $role->id,
                            'permission_id' => $permissionId,
                            'created_at' => $agora,
                            'updated_at' => $agora,
                        ]);
                    }
                }

                $role->clearCache();
            });
    }

    public function down(): void
    {
        $slugs = [
            'dashboard.gmail.show',
            'dashboard.gmail.anexo',
        ];

        $ids = Permission::whereIn('slug', $slugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        Permission::whereIn('slug', $slugs)->delete();

        Role::query()
            ->whereIn('slug', [
                'executiva',
                'secretariado_comum',
                'diretoria',
                'presidente',
                'tesouraria',
            ])
            ->get()
            ->each(fn (Role $role) => $role->clearCache());
    }
};
