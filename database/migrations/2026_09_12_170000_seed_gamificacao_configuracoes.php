<?php

use App\Models\Permission;
use App\Models\Role;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        app(GamificacaoConfiguracaoService::class)->semearParametros();

        $permissoes = [
            [
                'name' => 'Game CNM - Iniciar',
                'slug' => 'dashboard.game-cnm.index',
                'resource' => 'secretaria-estatistica',
            ],
            [
                'name' => 'Game CNM - Atualizar',
                'slug' => 'dashboard.game-cnm.update',
                'resource' => 'secretaria-estatistica',
            ],
            [
                'name' => 'Game CNM - Recalcular',
                'slug' => 'dashboard.game-cnm.recalcular',
                'resource' => 'secretaria-estatistica',
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
            ->whereIn('slug', ['administrador', 'secretaria_estatistica'])
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
            'dashboard.game-cnm.index',
            'dashboard.game-cnm.update',
            'dashboard.game-cnm.recalcular',
        ];

        $ids = Permission::whereIn('slug', $slugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        Permission::whereIn('slug', $slugs)->delete();
        DB::table('parametros')->where('area', 'gamificacao')->delete();

        Role::query()
            ->whereIn('slug', ['administrador', 'secretaria_estatistica'])
            ->get()
            ->each(fn (Role $role) => $role->clearCache());
    }
};
