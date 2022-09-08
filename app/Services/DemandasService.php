<?php

namespace App\Services;

use App\Helpers\FormHelper;
use App\Imports\DemandaImport;
use App\Models\Demanda;
use App\Models\DemandaItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class DemandasService
{
    public static function getCampos(Request $request) : array
    {
        try {

            $path = $request->arquivo->store('demandas');
            $classe = new DemandaImport();
            Excel::import($classe, request()->file('arquivo'));
            $retorno = [
                'informacoes' => $classe->informacoes->map(function($item) {
                    return [str_replace(' ', '_', $item) => $item];
                })->collapse()->toArray(),
                'path' => $path
            ];
            return $retorno;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public static function store(Request $request) : ?Demanda
    {
        DB::beginTransaction();
        try {
            $demanda = Demanda::create([
                'titulo' => $request->titulo,
                'path' => '/' . str_replace('public', 'storage', $request['path'])
            ]);

            $classe = new DemandaImport();
            Excel::import($classe, $request['path']);
            foreach ($classe->data as $row) {
                DemandaItem::create([
                    'origem' => $row['origem'],
                    'documento' => $row['doc_analisado'],
                    'nivel' => self::findNivel($row['nivel_de_prioridade']),
                    'demanda' => $row['demanda'],
                    'status' => 0,
                    'user_id' => self::findUser($row['atividade_para'], $request->campo),
                    'demanda_id' => $demanda->id
                ]);
            }
            DB::commit();
            return $demanda;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function update(Demanda $demanda, Request $request) : ?Demanda
    {
        try {
            $demanda->update([
                'titulo' => $request->titulo,
            ]);
            return $demanda;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(Demanda $demanda) : void
    {
        try {
            $demanda->itens()->delete();
            $demanda->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function atualizarItem(DemandaItem $item, array $request) : ?DemandaItem
    {

        try {
            $item->update([
                'origem' => $request['origem'],
                'documento' => $request['documento'],
                'demanda' => $request['demanda'],
                'status' => $request['status'],
                'user_id' => $request['user_id'],
            ]);
            return $item;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function storeItem(Demanda $demanda, array $request) : ?DemandaItem
    {

        try {
            $item = DemandaItem::create([
                'nivel' => $request['nivel'],
                'origem' => $request['origem'],
                'documento' => $request['documento'],
                'demanda' => $request['demanda'],
                'status' => $request['status'],
                'user_id' => $request['user_id'],
                'demanda_id' => $demanda->id
            ]);
            return $item;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function deleteItem(DemandaItem $item) : void
    {
        try {
            $item->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function findUser(string $chave, array $campos) : string
    {
        return isset($campos[str_replace(' ', '_', $chave)]) ? $campos[str_replace(' ', '_', $chave)] : null;
    }

    public static function findNivel(string $chave) : int
    {
        $nivel = 0;
        foreach (DemandaItem::NIVEIS as $key => $value) {
            if (strtolower(FormHelper::removerAcentos($chave)) == strtolower(FormHelper::removerAcentos($value))) {
                $nivel = $key;
            }
        }
        return $nivel;
    }

    public static function getUsuarios() : array
    {
        return User::whereHas('roles', function($sql) {
            return $sql->whereIn('name', [
                'diretoria',
                'executiva',
                'secretaria_eventos',
                'secretaria_produtos',
                'secretaria_evangelismo',
                'secretaria_responsabilidade',
                'secretaria_comunicacao',
                'secretaria_estatistica',
                'secretaria_educacao_crista',
            ]);
        })
        ->get()
        ->pluck('name', 'id')
        ->toArray();
    }

    public static function getStatus() : array
    {
        return DemandaItem::STATUS;
    }

    public static function getNiveis() : array
    {
        return DemandaItem::NIVEIS;
    }

}
