<?php

namespace App\Services;

use App\Models\Digesto;
use App\Models\LogErro;
use App\Models\TipoReuniao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class DigestoService
{
    
    public static function store(Request $request) : Digesto 
    {
        try {
            $path = $request->arquivo->store('public/disgesto');
            
            $digesto = Digesto::create([
                'tipo_reuniao_id' => $request->tipo_reuniao_id,
                'titulo' => $request->titulo,
                'ano' => $request->ano,
                'texto' => $request->texto,
                'path' => '/' . str_replace('public', 'storage', $path)
            ]);
            return $digesto;

        } catch (Throwable $th) {
            throw $th;
        }
    }
    
    public static function update(Digesto $digesto, Request $request) : Digesto 
    {
        try {
            
            $digesto->update([
                'tipo_reuniao_id' => $request->tipo_reuniao_id,
                'titulo' => $request->titulo,
                'ano' => $request->ano,
                'texto' => $request->texto,
            ]);
            return $digesto;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public static function delete(Digesto $digesto) : void
    {
        try {
            $digesto->delete();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public static function getTipos() : array
    {
        return TipoReuniao::all()->pluck('nome', 'id')->toArray();
    }

    public static function buscarItem() : array
    {
        if (!request()->anyFilled(['tipo_reuniao', 'ano', 'chave'])) {
            return [];
        }
        return Digesto::when(request()->filled('tipo_reuniao'), function($sql) {
            return $sql->where('tipo_reuniao_id', request()->tipo_reuniao);
        })
        ->when(request()->filled('ano'), function($sql) {
            return $sql->where('ano', request()->ano);
        })
        ->when(request()->filled('chave'), function($sql) {
            return $sql->where('texto', 'like', '%' . request()->chave . '%');
        })
        ->get()
        ->toArray();
    }
}