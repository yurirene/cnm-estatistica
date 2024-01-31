<?php

namespace App\Services;

use App\Models\Diretoria;
use App\Models\DiretoriaHistorico;
use App\Models\DiretoriaInformacao;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DiretoriaService
{
    public static function getDiretoriaVigente(): array
    {
        $diretoria = Diretoria::with('informacoes')
            ->daMinhaInstancia()
            ->first();
        if ($diretoria) {
            $diretoria = $diretoria->toArray();
        }
        $novo = empty($diretoria);
        return self::normalizar($diretoria, $novo);
    }

    public static function normalizar(?array $diretoria, bool $novo = false): array
    {
        $retorno = [];
        foreach (array_keys(Diretoria::LABELS) as $campo) {
            $retorno[$campo]['nome'] = $novo
                ? 'Não Informado'
                : $diretoria[$campo];
            $retorno[$campo]['path'] = $novo
                ? Diretoria::IMAGEM_PADRAO
                : $diretoria['informacoes']["path_$campo"];
            $retorno[$campo]['contato'] = $novo
                ? '(xx) xxxxx-xxxx'
                : $diretoria['informacoes']["contato_$campo"];
        }

        $retorno['id'] = !$novo ? $diretoria['id'] : null;

        return $retorno;
    }

    /**
     * Valida se o ano da nova diretoria está disponível para cadastro
     *
     * @param integer $ano
     * @return void
     */
    public static function validarAnoDaDiretoria(int $ano): bool
    {
        return Diretoria::daMinhaInstancia()->where('ano', $ano)->get()->isEmpty();
    }

    /**
     * Método responsável por limpar os dados da diretoria para novo cadastro e remover os secretarios
     *
     * @param int ano
     * @return void
     */
    public static function novaDiretoria(int $ano): void
    {
        DB::beginTransaction();
        try {
            $diretoria = Diretoria::select()->daMinhaInstancia()->first();
            $informacao = DiretoriaInformacao::where('diretoria_id', $diretoria->id)->first();
            foreach (array_keys(Diretoria::LABELS) as $campo) {
                $diretoria[$campo] = 'Não Informado';
                $informacao["path_$campo"] = Diretoria::IMAGEM_PADRAO;
                $informacao["contato_$campo"] = null;
            }
            $diretoria->ano = $ano;
            $diretoria->save();
            $informacao->save();
            $diretoria->secretarios()->delete();
            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Método responsável por salvar os dados antigos no histórico
     *
     * @return void
     */
    public static function salvarNoHistorico(): void
    {
        DB::beginTransaction();
        try {
            $diretoria = Diretoria::select()->daMinhaInstancia()->first();
            $secreatrios = !empty($diretoria->secreatrios)
                ? $diretoria->secreatrios()->select('nome, secretaria')->get()->toArray()
                : [];
            $historico = [
                'diretoria' => $diretoria->toArray(),
                'secreatarios' => $secreatrios
            ];

            DiretoriaHistorico::create([
                'diretoria_id' => $diretoria->id,
                'ano' => $diretoria->ano,
                'historico' => json_encode($historico)
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    /**
     * Método responsável por atualizar os dados do cargo
     *
     * @param array $request
     * @return void
     */
    public static function update(array $request): void
    {
        DB::beginTransaction();
        try {
            $diretoria = Diretoria::select()->daMinhaInstancia()->first();
            $diretoria[$request['chave']] = $request['nome'];
            $diretoria->save();
            $informacoes = $diretoria->informacoes;
            $informacoes["contato_{$request['chave']}"] = $request['contato'];
            if (!empty($request['imagem'])) {
                $nome = time().'.'.$request['imagem']->getClientOriginalExtension();
                $path = $request['imagem']->storeAs("/public/diretorias/{$diretoria->id}", $nome);
                $pathAntiga = str_replace('storage', 'public', $informacoes["path_{$request['chave']}"]);
                if (Storage::exists($pathAntiga)) {
                    Storage::delete($pathAntiga);
                }
                $informacoes["path_{$request['chave']}"] = str_replace('public', 'storage', $path);
            }
            $informacoes->save();

            DB::commit();
        } catch (Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            throw $th;
        }
    }

}
