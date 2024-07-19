<?php

namespace App\Services;

use App\Models\Diretoria;
use App\Models\DiretoriaHistorico;
use App\Models\DiretoriaInformacao;
use App\Models\Secretario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SecretarioService
{

    public static function store(array $request): void
    {
        try {
            $secretario = Secretario::create([
                'secretaria' => $request['secretaria'],
                'nome' => $request['nome_secretario'],
                'contato' => $request['contato_secretario'],
                'diretoria_id' => $request['diretoria_id'],
                'path' => Diretoria::IMAGEM_PADRAO
            ]);

            if (!empty($request['imagem_secretario'])) {
                $nome = time().'.'.$request['imagem_secretario']->getClientOriginalExtension();
                $path = $request['imagem_secretario']->storeAs("/public/diretorias/{$request['diretoria_id']}", $nome);
                $secretario->path = str_replace('public', 'storage', $path);
                $secretario->save();
            }

        } catch (\Throwable $e) {
            LogErroService::registrar([
                'msg'=> $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }


    public static function update(array $request): void
    {
        try {
            $secretario = Secretario::find($request['secretario_id']);
            $secretario->update([
                'secretaria' => $request['secretaria'],
                'nome' => $request['nome_secretario'],
                'contato' => $request['contato_secretario'],
                'diretoria_id' => $request['diretoria_id']
            ]);

            if (!empty($request['imagem_secretario'])) {
                $nome = time().'.'.$request['imagem_secretario']->getClientOriginalExtension();
                $path = $request['imagem_secretario']->storeAs("/public/diretorias/{$request['diretoria_id']}", $nome);
                $pathAntiga = str_replace('storage', 'public', $secretario->path);
                if (Storage::exists($pathAntiga)) {
                    Storage::delete($pathAntiga);
                }
                $secretario->path = str_replace('public', 'storage', $path);
                $secretario->save();
            }

        } catch (\Throwable $e) {
            LogErroService::registrar([
                'msg'=> $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }

    /**
     * Retorna o array com as informações dos secretários
     *
     * @param string $diretoriaId
     * @return array
     */
    public static function getSecretariosDaDiretoria(string $diretoriaId): array
    {
        return Secretario::where('diretoria_id', $diretoriaId)->get()->toArray();
    }

    public static function delete(string $secretarioId): void
    {
        Secretario::findOrFail($secretarioId)->delete();
    }

}
