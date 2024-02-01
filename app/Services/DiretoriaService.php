<?php

namespace App\Services;

use App\Models\Diretoria;
use App\Models\DiretoriaHistorico;
use App\Models\DiretoriaInformacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DiretoriaService
{
    /**
     * Retorna o registro da diretoria
     *
     * @return App\Model\Diretoria
     */
    public static function getDiretoriaVigente(): ?Diretoria
    {
        return Diretoria::with('informacoes')
            ->daMinhaInstancia()
            ->first();
    }

    /**
     * Retorna os dados formtados da diretoria e caso não haja diretoria
     * cadastra uma
     *
     * @return array
     */
    public static function getDadosDiretoria(): array
    {
        $diretoria = self::getDiretoriaVigente();

        if (empty($diretoria)) {
            DiretoriaService::gerarDiretoria();
            $diretoria = self::getDiretoriaVigente();

        }
        $diretoria = $diretoria->toArray();
        return self::agruparInformacoes($diretoria);
    }

    /**
     * Método responsável por agrupar informações da diretoria
     *
     * @param array|null $diretoria
     * @return array
     */
    public static function agruparInformacoes(array $diretoria): array
    {
        $retorno = [];
        $campos = self::getCamposDaInstancia();

        foreach (array_keys($campos) as $campo) {
            $retorno['membros'][$campo]['nome'] = $diretoria[$campo];
            $retorno['membros'][$campo]['path'] = $diretoria['informacoes']["path_$campo"] ?? Diretoria::IMAGEM_PADRAO;
            $retorno['membros'][$campo]['contato'] = $diretoria['informacoes']["contato_$campo"];
            $retorno['membros'][$campo]['cargo'] = $campos[$campo];
        }

        $retorno['id'] = $diretoria['id'];

        return $retorno;
    }

    public static function getCamposDaInstancia(): array
    {
        $campos = Diretoria::LABELS;
        $instancia = auth()->user()->instancia_formatada;
        if ($instancia == 'Sinodal') {
            $campos['secretario_causas'] = 'Secretário Sinodal';
        }

        if ($instancia == 'Federação') {
            $campos['secretario_causas'] = 'Secretário Presbiterial';
        }

        if ($instancia == 'Local') {
            $campos['secretario_causas'] = 'Conselheiro';
            unset($campos['secretario_executivo']);
        }
        return $campos;
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
            DB::rollBack();
            throw $th;
        }
    }

    public static function gerarDiretoria(): void
    {
        DB::beginTransaction();
        try {
            $campo = UserService::getCampoInstanciaDB();
            $diretoria = Diretoria::create([
                'presidente' => 'Não Informado',
                'vice_presidente' => 'Não Informado',
                'primeiro_secretario' => 'Não Informado',
                'segundo_secretario' => 'Não Informado',
                'secretario_executivo' => 'Não Informado',
                'tesoureiro' => 'Não Informado',
                'secretario_causas' => 'Não Informado',
                $campo['campo'] => $campo['id'],
                'ano' => date('Y')
            ]);
            DiretoriaInformacao::create([
                'diretoria_id' => $diretoria->id
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

}
