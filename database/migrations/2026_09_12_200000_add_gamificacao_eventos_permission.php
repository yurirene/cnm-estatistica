<?php

use App\Models\Parametro;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Parametro::query()
            ->where('area', GamificacaoConfiguracaoService::AREA)
            ->where('nome', 'pilares.resgate.maximo_percentual_evangelismo')
            ->update(['valor' => '60']);
        Cache::forget('gamificacao.config.overrides');

        $permissoes = [
            [
                'name' => 'Game CNM - Eventos',
                'slug' => 'dashboard.game-cnm.eventos.index',
                'resource' => 'secretaria-estatistica',
            ],
            [
                'name' => 'Game CNM - Cadastrar evento',
                'slug' => 'dashboard.game-cnm.eventos.store',
                'resource' => 'secretaria-estatistica',
            ],
            [
                'name' => 'Game CNM - Remover evento',
                'slug' => 'dashboard.game-cnm.eventos.destroy',
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
        Parametro::query()
            ->where('area', GamificacaoConfiguracaoService::AREA)
            ->where('nome', 'pilares.resgate.maximo_percentual_evangelismo')
            ->update(['valor' => '50']);
        Cache::forget('gamificacao.config.overrides');

        $slugs = [
            'dashboard.game-cnm.eventos.index',
            'dashboard.game-cnm.eventos.store',
            'dashboard.game-cnm.eventos.destroy',
        ];

        $ids = Permission::whereIn('slug', $slugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        Permission::whereIn('slug', $slugs)->delete();

        Role::query()
            ->whereIn('slug', ['administrador', 'secretaria_estatistica'])
            ->get()
            ->each(fn (Role $role) => $role->clearCache());
    }
};
