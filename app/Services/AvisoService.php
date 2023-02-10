<?php

namespace App\Services;

use App\Models\Aviso;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AvisoService
{

    /**
     * Salvar aviso
     *
     * @param array $request
     * @return void
     */
    public static function store(array $request): void
    {
        DB::beginTransaction();
        try {
            $aviso = Aviso::create([
                'titulo' => $request['titulo'],
                'texto' => $request['texto'],
                'tipo' => $request['tipo'],
                'modal' => isset($request['modal'])
            ]);

            if ($request['tipo'] == Aviso::LOCAL) {
                $locais = Local::where('status', true)->get()->map(function ($item) {
                    return [
                        $item->id => [
                            'user_id' => $item->usuario()->first()->id
                        ]
                    ];
                })
                ->collapse()
                ->toArray();
                $aviso->locais()->attach($locais);
            }

            if ($request['tipo'] == Aviso::FEDERACAO) {
                $federacoes = Federacao::where('status', true)->get()->map(function ($item) {
                    return [
                        $item->id => [
                            'user_id' => $item->usuario()->first()->id
                        ]
                    ];
                })
                ->collapse()
                ->toArray();
                $aviso->federacoes()->attach($federacoes);
            }

            if ($request['tipo'] == Aviso::SINODAL) {
                $sinodais = Sinodal::where('status', true)->get()->map(function ($item) {
                    return [
                        $item->id => [
                            'user_id' => $item->usuario()->first()->id
                        ]
                    ];
                })
                ->collapse()
                ->toArray();
                $aviso->sinodais()->attach($sinodais);
            }

            if ($request['tipo'] == Aviso::CUSTOM && !empty($request['usuarios'])) {

                $aviso->usuarios()->attach($request['usuarios']);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;

        }
    }

    /**
     * Retornar usuários para o select2 de avisos
     */

    public static function getUsuarios(): Collection
    {
        return User::where('status', 1)
            ->when(request()->has('term'), function ($sql) {
                return $sql->whereHas('sinodais', function ($q) {
                    return $q->where('nome', 'like', "%".request('term')."%");
                })
                ->orWhereHas('federacoes', function ($q) {
                    return $q->where('nome', 'like', "%".request('term')."%");
                })
                ->orWhereHas('locais', function ($q) {
                    return $q->where('nome', 'like', "%".request('term')."%");
                });
            })
            ->get()
            ->map(function ($item) {
                $nome = $item->instancia() ? $item->instancia()->first()->nome : $item->name;
                $regiao = $item->instancia() ? $item->instancia()->first()->regiao->nome : '-';
                return [
                    'id' => $item->id,
                    'text' => "{$nome} - {$regiao}"
                ];
            });
    }

    /**
     * Marcar aviso como visualizado
     *
     * @param [type] $id
     * @return array
     */
    public static function visualizado($id): array
    {
        $usuario = auth()->user();

        $usuario->avisos()->updateExistingPivot($id, ['visualizado' => true], false);
        return [];
    }

    /**
     * Apagar Aviso
     *
     * @param [type] $id
     * @return void
     */
    public static function delete($id): void
    {
        Aviso::find($id)->delete();
    }

}
