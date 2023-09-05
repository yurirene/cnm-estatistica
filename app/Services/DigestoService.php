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

    public const PATH_DIR = 'storage/disgesto/';
    public const PATH_SERVER = 'public/disgesto/';

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
            if ($request->has('arquivo')) {
                $path = $request->arquivo->store('public/disgesto');
                $digesto->update([
                    'path' => '/' . str_replace('public', 'storage', $path)
                ]);
            }
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
        ->map(function($item) {
            $texto = '';
            if (request()->filled('chave')) {
                $inicio = strpos($item->texto, request()->chave);
                $texto = substr($item->texto, $inicio, 60);
            }
            $item->texto_formatado = $texto;
            $item->path = str_replace('/' . self::PATH_DIR, '', $item->path);
            return $item;
        })
        ->toArray();
    }

    /**
     * Método que verifica se o arquivo é doc ou docx e ao inves de
     * exibir (arquivo binário está sendo exibido) força o download
     *
     * @param string $path
     * @return mixed
     */
    public static function exibir(string $path)
    {
        $posicaoDoc = strpos($path, '.doc');
        if ($posicaoDoc) {
            $novoNome = 'digesto_' . date('ymdhis') . substr($path, $posicaoDoc);
            return response()->download(self::PATH_DIR . $path, $novoNome);
        }
        if (! file_exists(self::PATH_DIR.$path)) {
            abort(404, 'Aquivo não encontrado!');
        }
        return response()->file(self::PATH_DIR . $path);
    }
}
