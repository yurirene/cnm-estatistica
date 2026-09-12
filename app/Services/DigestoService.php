<?php

namespace App\Services;

use App\Models\Digesto;
use App\Models\TipoReuniao;
use Illuminate\Http\Request;
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
            $termo = self::formatarTermoFullText(request()->chave);

            if ($termo === '') {
                return $sql->whereRaw('1 = 0');
            }

            return $sql->whereRaw(
                'MATCH(titulo, texto) AGAINST(? IN BOOLEAN MODE)',
                [$termo]
            )->orderByRaw(
                'MATCH(titulo, texto) AGAINST(? IN BOOLEAN MODE) DESC',
                [$termo]
            );
        })
        ->get()
        ->map(function($item) {
            $texto = '';
            if (request()->filled('chave')) {
                $inicio = mb_stripos($item->texto, request()->chave);
                if ($inicio === false) {
                    foreach (preg_split('/\s+/', request()->chave, -1, PREG_SPLIT_NO_EMPTY) as $palavra) {
                        $inicio = mb_stripos($item->texto, $palavra);
                        if ($inicio !== false) {
                            break;
                        }
                    }
                }
                $texto = $inicio !== false ? mb_substr($item->texto, $inicio, 60) : '';
            }
            $item->texto_formatado = $texto;
            $item->path = str_replace('/' . self::PATH_DIR, '', $item->path);
            return $item;
        })
        ->toArray();
    }

    /**
     * Monta o termo da busca FULLTEXT em BOOLEAN MODE.
     * Cada palavra vira +palavra* (obrigatória e com prefixo).
     */
    private static function formatarTermoFullText(string $chave): string
    {
        $chave = trim($chave);

        if ($chave === '') {
            return '';
        }

        if (preg_match('/^".+"$/', $chave)) {
            $frase = trim($chave, '"');
            $frase = preg_replace('/[+\-><()~*@]+/', ' ', $frase);
            $frase = trim(preg_replace('/\s+/', ' ', $frase));

            return $frase !== '' ? '"' . $frase . '"' : '';
        }

        $termos = preg_split('/\s+/', $chave, -1, PREG_SPLIT_NO_EMPTY);

        return collect($termos)
            ->map(function (string $termo) {
                $termo = preg_replace('/[+\-><()~*"@]+/', '', $termo);

                if ($termo === '' || mb_strlen($termo) < 3) {
                    return null;
                }

                return '+' . $termo . '*';
            })
            ->filter()
            ->implode(' ');
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
