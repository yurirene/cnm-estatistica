<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valores_aci_ano', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('ano')->unique();
            $table->decimal('valor', 8, 2);
            $table->timestamps();
        });

        $this->popularAnosExistentes();
        $this->liberarPermissao();
    }

    public function down(): void
    {
        $slug = 'dashboard.estatistica.atualizarValorAciAno';
        $ids = Permission::where('slug', $slug)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        Permission::where('slug', $slug)->delete();

        Role::query()
            ->whereIn('slug', ['administrador', 'secretaria_estatistica'])
            ->get()
            ->each(fn (Role $role) => $role->clearCache());

        Schema::dropIfExists('valores_aci_ano');
    }

    private function popularAnosExistentes(): void
    {
        $parametro = DB::table('parametros')->where('nome', 'valor_aci')->first();
        if ($parametro === null) {
            return;
        }

        $valor = $this->parseValor($parametro->valor);
        $anos = collect();

        $anoReferencia = DB::table('parametros')->where('nome', 'ano_referencia')->value('valor');
        if ($anoReferencia !== null && $anoReferencia !== '') {
            $anos->push((int) $anoReferencia);
        }

        foreach ([
            ['comprovantes_aci', 'ano'],
            ['formularios_sinodal_v1', 'ano_referencia'],
            ['formularios_federacao_v1', 'ano_referencia'],
        ] as [$tabela, $coluna]) {
            if (Schema::hasTable($tabela)) {
                $anos = $anos->merge(DB::table($tabela)->distinct()->pluck($coluna));
            }
        }

        $agora = now();
        foreach ($anos->filter()->map(fn ($ano) => (int) $ano)->unique()->sort() as $ano) {
            if ($ano <= 0) {
                continue;
            }

            DB::table('valores_aci_ano')->insertOrIgnore([
                'ano' => $ano,
                'valor' => $valor,
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);
        }
    }

    private function parseValor(mixed $valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        $texto = trim((string) $valor);
        if ($texto === '') {
            return 0.0;
        }

        $semMilhar = str_replace('.', '', $texto);
        $normalizado = str_replace(',', '.', $semMilhar);

        return is_numeric($normalizado) ? (float) $normalizado : 0.0;
    }

    private function liberarPermissao(): void
    {
        $permissao = Permission::updateOrCreate(
            ['slug' => 'dashboard.estatistica.atualizarValorAciAno'],
            [
                'name' => 'Módulo Secretaria-estatistica - atualizarValorAciAno',
                'resource' => 'secretaria-estatistica',
            ]
        );

        $agora = now();
        Role::query()
            ->whereIn('slug', ['administrador', 'secretaria_estatistica'])
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
};
