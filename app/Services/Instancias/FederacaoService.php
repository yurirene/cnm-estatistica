<?php

namespace App\Services\Instancias;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\LogErroService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FederacaoService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $regiao = Sinodal::find($request->sinodal_id)->regiao_id;
            $federacao = Federacao::create([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);


            $usuario = UserService::usuarioVinculado($request, $federacao, 'federacao', 'federacoes');
            if ($request->has('resetar_senha')) {
                UserService::resetarSenha($usuario);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Salvar");

        }
    }

    public static function update(Federacao $federacao, Request $request)
    {
        DB::beginTransaction();
        try {
            $regiao = Sinodal::find($request->sinodal_id)->regiao_id;
            $federacao->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);

            $usuario = UserService::usuarioVinculado($request, $federacao, 'federacao', 'federacoes');
            if ($request->has('resetar_senha')) {
                UserService::resetarSenha($usuario);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Atualizar");

        }
    }

    public static function getEstados()
    {
        $sinodais = Auth::user()->sinodais;
        $regioes = [];
        foreach ($sinodais as $sinodal) {
            $regioes[] = $sinodal->regiao_id;
        }
        $regioes = Estado::whereIn('regiao_id', $regioes)
            ->get()
            ->pluck('nome', 'id');
        return $regioes;
    }

    public static function getSinodal()
    {
        $usuario = User::find(Auth::id());
        $regioes = Sinodal::whereIn('regiao_id', $usuario->regioes->pluck('id'))
            ->get()
            ->pluck('nome', 'id');
        return $regioes;
    }


    public static function updateInfo(Federacao $federacao, Request $request)
    {
        DB::beginTransaction();
        try {
            $federacao->update([
                'nome' => $request->nome,
                'presbiterio' => $request->presbiterio,
                'data_organizacao' => Carbon::createFromFormat('d/m/Y', $request->data_organizacao)->format('Y-m-d'),
                'midias_sociais' => $request->midias_sociais
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Atualizar");

        }
    }

    public static function getInfo()
    {
        try {
            return Auth::user()->federacoes->first();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function getTotalizadores()
    {
        $federacao = Auth::user()->federacoes->first();
        try {
            $formulario = FormularioFederacao::where('federacao_id', $federacao->id)
                ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
                ->first();
            if (!$formulario) {
                return [
                    'total_umps' => $federacao->locais->count(),
                    'total_socios' => 'Resposta Pendente',
                ];
            }
            return [
                'total_umps' => $formulario->estrutura['ump_organizada'] ?? 0,
                'total_socios' => intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores'])
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(Federacao $federacao)
    {
        DB::beginTransaction();
        try {
            if ($federacao->usuario->first()) {
                $federacao->usuario->first()->update([
                    'email' => 'apagadoFedEm'.date('dmyhms').'@apagado.com'
                ]);
                $usuario = $federacao->usuario->first();
                $federacao->usuario()->sync([]);
                $usuario->delete();
            }


            $federacao->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function getTotalUmpsOrganizadas(Federacao $federacao, FormularioFederacao $formulario = null) : array
    {
        if (!is_null($formulario)) {
            $total = ($formulario->estrutura['ump_organizada'] ?? 0) + ($formulario->estrutura['ump_nao_organizada'] ?? 0);
            return [
                'total' => $total,
                'organizadas' => $formulario->estrutura['ump_organizada'] ?? 0
            ];
        }
        return [
            'total' => $federacao->locais->count(),
            'organizadas' => $federacao->locais->where('status', true)->count()
        ];
    }


    public static function getInformacoesFederacaoOrganizacao(Federacao $federacao) : array
    {
        try {
            $formulario = FormularioFederacao::where('federacao_id', $federacao->id)->orderBy('created_at', 'desc')->get()->first();

            $total_umps_organizada = self::getTotalUmpsOrganizadas($federacao, $formulario);

            $total_umps_organizada = SinodalService::getPorcentagem($total_umps_organizada['total'], $total_umps_organizada['organizadas']);
            $total_igrejas_n_sociedades = SinodalService::getPorcentagem($federacao->locais->count(), $federacao->locais->where('outro_modelo', true)->count());

            return [
                'ultimo_formulario' => $formulario ? $formulario->ano_referencia : 'Sem Resposta',
                'total_umps_organizada' => $total_umps_organizada,
                'total_igrejas_n_sociedades' => $total_igrejas_n_sociedades
            ];
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function getInformacoesLocaisShow(Federacao $federacao) : array
    {
        try {

            $locais = $federacao->locais()->orderBy('status', 'desc')->get();
            $info_local = [];
            foreach ($locais as $local) {
                $utlimo_formulario = $local->relatorios()->orderBy('created_at','desc')->get()->first();

                $ultimo_ano = 'Sem Resposta';
                $total_socios = 0;
                if (!is_null($utlimo_formulario)) {
                    $total_socios = intval($utlimo_formulario->perfil['ativos'] ?? 0) + intval($utlimo_formulario->perfil['cooperadores'] ?? 0);
                    $ultimo_ano = $utlimo_formulario->ano_referencia;
                }


                $info_local[] = [
                    'id' => $local->id,
                    'nome' => $local->nome,
                    'status' => $local->status,
                    'numero_socios' => $total_socios,
                    'ultimo_formulario' => $ultimo_ano
                ];
            }
            return $info_local;

        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }


    /**
     * Retorna a lista de federacao em sessão vinculadas ao usuário
     *
     * @return array
     */
    public static function getListaFederacoes(): array
    {
        if (session()->has('lista_federacoes')) {
            return session()->get('lista_federacoes');
        }
        $federacoes = Federacao::select(['id']);
        if (!auth()->user()->admin) {
            $federacoes = $federacoes->whereIn('sinodal_id', auth()->user()->sinodais->pluck('id')->toArray());
        }
        $listaFederacoes = $federacoes->orderBy('nome')->get()->pluck('id')->toArray();

        session()->put('lista_federacoes', $listaFederacoes);
        return $listaFederacoes;
    }

    /**
     * Retorna o id da próxima federacao e da anterior para navegação na tela de detalhes
     *
     * @param string $sinodalAtual
     * @return array
     */
    public static function navegacaoListaFederacoes(string $sinodalAtual): array
    {
        $listaFederacoes = self::getListaFederacoes();
        $chave = array_search($sinodalAtual, $listaFederacoes);

        return [
            'anterior' => $chave-1 < 0 ? null : $listaFederacoes[$chave-1],
            'proxima' => isset($listaFederacoes[$chave+1]) ? $listaFederacoes[$chave+1] : null
        ];
    }
}
