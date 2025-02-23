<?php

namespace App\Services\Apps;

use App\Models\Apps\Site\ModeloSite;
use App\Models\Apps\Site\Site;
use App\Models\Sinodal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Service responsável por manipular as configurações dos sites
 * Como Funciona:
 * - Ao abrir a tela de configuração, não havendo site registrado, é
 * cadastrado um novo site através do método criarSite
 * - As configurações são organizadas por tipos: Editáveis e Não editaveis
 * -- Para referenciar uma configuração é necessário informar a chave da configuração,
 *    que é a sua posição no array de configurações editáveis
 *
 * Quando criar uma nova funcionalidade adicionar nas configurações do modelo
 * "ModeloSiteSeeder.php" e rodar o "SiteAtualizarConfigSeeder.php"
 *
 */
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
                'url' => "https://ump.app.br/site/" . strtolower($sinodal->sigla)
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function atualizarConfig(string $id, array $request)
    {
        try {
            $sinodal = Sinodal::find($id);
            $site = $sinodal->site;
            $config = $site->configuracoes;
            $config['editaveis'][$request['chave']][$request['config']] = $request['valor'];

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
            $diretoria = self::montarDiretoria($editaveis, $sinodal);

            return array_merge(
                $editaveis,
                [
                    'galeria' => $galeria,
                    'sigla' => $sinodal->sigla,
                    'federacoes' => $federacoes,
                    'totalizador' => $totalizador,
                    'evento_url' => $sinodal->site->url . '/evento',
                    'evento_status' => $sinodal->evento->status ?? false,
                    'diretoria' => $diretoria
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
            $foto = $sinodal->galeria()
            ->where('id', $galeria_id)
            ->first();
            Storage::delete(str_replace('storage', 'public', $foto));

            $foto->delete();
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
            Storage::delete(
                str_replace(
                    'storage',
                    'public',
                    $config['editaveis'][$request['chave']]['diretoria'][$request['cargo']]['path']
                )
            );
            $nome = time().'.'.$request->file('foto')->getClientOriginalExtension();
            $path = $request->file('foto')->storeAs("/public/sinodais/{$sinodal_id}/diretoria", $nome);

            $config['editaveis'][$request['chave']]['diretoria'][$request['cargo']]['path'] = str_replace(
                'public',
                'storage',
                $path
            );
            $site->update([
                'configuracoes' => $config
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function novaSecretaria(string $sinodal_id, Request $request)
    {

        try {

            $sinodal = Sinodal::find($sinodal_id);
            $site = $sinodal->site;
            $config = $site->configuracoes;

            $chave = is_null($request['chave_secretaria']) || $request['novo']
                ? count($config['editaveis'][$request['chave']]['secretarias'])
                : $request['chave_secretaria'];


            $pathFinal = !empty($config['editaveis'][$request['chave']]['secretarias'][$chave]['path'])
                ? $config['editaveis'][$request['chave']]['secretarias'][$chave]['path']
                : "";
            if ($request->has('foto')) {
                if (
                    !$request['novo']
                    && !empty($config['editaveis'][$request['chave']]['secretarias'][$chave]['path'])
                ) {
                    Storage::delete(
                        str_replace(
                            'storage',
                            'public',
                            $config['editaveis'][$request['chave']]['secretarias'][$chave]['path']
                        )
                    );
                }
                $nome = time().'.'.$request->file('foto')->getClientOriginalExtension();
                $path = $request->file('foto')->storeAs("/public/sinodais/{$sinodal_id}/secretarias", $nome);
                $pathFinal = str_replace(
                    'public',
                    'storage',
                    $path
                );
            }
            $config['editaveis'][$request['chave']]['secretarias'][$chave]['nome'] = $request->nome_secretario;
            $config['editaveis'][$request['chave']]['secretarias'][$chave]['secretaria'] = $request->nome_secretaria;
            $config['editaveis'][$request['chave']]['secretarias'][$chave]['path'] = $pathFinal;

            $site->update([
                'configuracoes' => $config
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function removerSecretaria(string $sinodalId, int $config, int $chave)
    {
        try {

            $sinodal = Sinodal::find($sinodalId);
            $site = $sinodal->site;
            $configuracao = $site->configuracoes;
            $secretaria = $configuracao['editaveis'][$config]['secretarias'][$chave] ?? null;
            if (!empty($secretaria)) {
                if (!empty($secretaria['path'])) {
                    Storage::delete(
                        str_replace(
                            'storage',
                            'public',
                            $configuracao['editaveis'][$config]['secretarias'][$chave]['path']
                        )
                    );
                }
                unset($configuracao['editaveis'][$config]['secretarias'][$chave]);
            }
            $configuracao['editaveis'][$config]['secretarias'] = array_values(
                $configuracao['editaveis'][$config]['secretarias']
            );
            $site->update([
                'configuracoes' => $configuracao
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function montarDiretoria(array $editaveis, Sinodal $sinodal): array
    {
        $diretoria = $sinodal->diretoria;
        $retorno = [];
        foreach ($editaveis['diretoria'] as $key => $dado) {
            $campo = $dado['cargo'];
            $retorno[$key] = $dado;
            $retorno[$key]['nome'] = $diretoria->$campo ?? '';
        }

        return $retorno;
    }

}
