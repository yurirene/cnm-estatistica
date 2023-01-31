<?php

namespace App\Services\Apps;

use App\Models\Apps\Site\ModeloSite;
use App\Models\Apps\Site\Site;
use App\Models\Sinodal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteService
{

    public static function criarSite(Sinodal $sinodal): ?Site
    {
        try {
            $modelo = ModeloSite::first();
            return Site::create([
                'sinodal_id' => $sinodal->id,
                'modelo_id' => $modelo->id,
                'configuracoes' => $modelo->configuracoes,
                'url' => "https://ump.app.br/site/" . strtolower(str_replace(['-', ' '], '', $sinodal->sigla))
            ]);

        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw $th;
        }
    }

    public static function atualizarConfig(string $id, array $request)
    {
        try {
            $sinodal = Sinodal::find($id);
            $site = $sinodal->site;
            $config = $site->configuracoes;
            if (isset($request['cargo'])) {
                $config['editaveis'][$request['chave']]['diretoria'][$request['cargo']]['nome'] = $request['valor'];
            } else {
                $config['editaveis'][$request['chave']][$request['config']] = $request['valor'];
            }
            $site->update([
                'configuracoes' => $config
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function montar(Sinodal $sinodal, array $configuracoes): array
    {
        try {
            $editaveis = collect($configuracoes['editaveis'])->collapse()->toArray();
            $federacoes = $sinodal->federacoes->map(function ($item) {
                return "{$item->sigla} - {$item->nome}";
            })->toArray();
            $relatorio = $sinodal->relatorios()->get()->last();
            $totalizador = [
                'umps' => $relatorio->estrutura['ump_organizada'] ?? 0,
                'federacao' => $relatorio->estrutura['federacao_organizada'] ?? 0,
                'socios' => intval($relatorio->perfil['ativos'] ?? 0) + intval($relatorio->perfil['cooperadores'] ?? 0)
            ];
            $galeria  = $sinodal->galeria->map(function ($item) {
                return $item->path;
            });
            return array_merge(
                $editaveis,
                [
                    'galeria' => $galeria,
                    'sigla' => $sinodal->sigla,
                    'nomeSinodal' => $sinodal->nome,
                    'federacoes' => $federacoes,
                    'totalizador' => $totalizador
                ]
            );

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function adicionarNaGaleria(string $sinodal_id, Request $request)
    {
        try {
            if (!$request->hasFile('foto')) {
                return;
            }
            $nome = time().'.'.$request->file('foto')->getClientOriginalExtension();
            $path = $request->file('foto')->storeAs("/public/sinodais/{$sinodal_id}/galeria", $nome);
            $sinodal = Sinodal::find($sinodal_id);
            $sinodal->galeria()->create([
                'path' => str_replace('public', 'storage', $path)
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function removerDaGaleria(string $sinodal_id, int $galeria_id)
    {
        try {
            $sinodal = Sinodal::find($sinodal_id);
            $sinodal->galeria()
                ->where('id', $galeria_id)
                ->first()
                ->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function atualizarFotoDiretoria(string $sinodal_id, Request $request)
    {
        try {
            if (!$request->hasFile('foto')) {
                return;
            }

            $sinodal = Sinodal::find($sinodal_id);
            $site = $sinodal->site;
            $config = $site->configuracoes;
            Storage::delete(str_replace('storage', 'public', $config['editaveis'][$request['chave']]['diretoria'][$request['cargo']]['path']));
            $nome = time().'.'.$request->file('foto')->getClientOriginalExtension();
            $path = $request->file('foto')->storeAs("/public/sinodais/{$sinodal_id}/diretoria", $nome);

            $config['editaveis'][$request['chave']]['diretoria'][$request['cargo']]['path'] = str_replace('public', 'storage', $path);
            $site->update([
                'configuracoes' => $config
            ]);

        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw $th;
        }
    }

}
