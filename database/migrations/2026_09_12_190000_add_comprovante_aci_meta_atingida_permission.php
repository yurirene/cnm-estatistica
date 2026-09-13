<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissao = Permission::updateOrCreate(
            ['slug' => 'dashboard.comprovante-aci.meta-atingida'],
            [
                'name' => 'Comprovante ACI - Meta atingida',
                'slug' => 'dashboard.comprovante-aci.meta-atingida',
                'resource' => 'comprovante-aci',
            ]
        );

        $agora = now();
        Role::query()
            ->whereIn('slug', ['tesouraria', 'administrador'])
            ->get()
            ->each(function (Role $role) use ($permissao, $agora) {
                $existe = DB::table('permission_role')
                    ->where('role_id', $role->id)
                    ->where('permission_id', $permissao->id)
                    ->exists();

                if (! $existe) {
                    DB::table('permission_role')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permissao->id,
                        'created_at' => $agora,
                        'updated_at' => $agora,
                    ]);
                }
                $role->clearCache();
            });
    }

    public function down(): void
    {
        $slug = 'dashboard.comprovante-aci.meta-atingida';
        $id = Permission::where('slug', $slug)->value('id');
        if ($id) {
            DB::table('permission_role')->where('permission_id', $id)->delete();
            Permission::where('id', $id)->delete();
        }

        Role::query()
            ->whereIn('slug', ['tesouraria', 'administrador'])
            ->get()
            ->each(fn (Role $role) => $role->clearCache());
    }
};
