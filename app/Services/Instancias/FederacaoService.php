<?php

namespace App\Services\Instancias;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
use App\Services\LogErroService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        return Sinodal::whereIn('regiao_id', $usuario->regioes->pluck('id'))
            ->get()
            ->pluck('nome', 'id');
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
        return auth()->user()->federacoes->first();
    }


    public static function getTotalizadores()
    {
        $federacao = self::getInfo();
        try {
            $formulario = FormularioFederacao::where('federacao_id', $federacao->id)
                ->where('ano_referencia', EstatisticaService::getAnoReferencia())
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
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
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
            $total = ($formulario->estrutura['ump_organizada'] ?? 0)
                + ($formulario->estrutura['ump_nao_organizada'] ?? 0);
            return [
                'total' => $total,
                'organizadas' => $formulario->estrutura['ump_organizada'] ?? 0,
                'relatorio' => true
            ];
        }
        return [
            'total' => $federacao->locais->count(),
            'organizadas' => $federacao->locais->where('status', true)->count(),
            'relatorio' => false
        ];
    }


    public static function getInformacoesFederacaoOrganizacao(Federacao $federacao) : array
    {
        try {
            $formulario = FormularioFederacao::where('federacao_id', $federacao->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->first();

            $totalUmpsOrganizada = self::getTotalUmpsOrganizadas($federacao, $formulario);

            $totalUmpsOrganizada = SinodalService::getPorcentagem(
                $totalUmpsOrganizada['total'],
                $totalUmpsOrganizada['organizadas']
            );
            $totalIgrejasNSociedades = SinodalService::getPorcentagem(
                $federacao->locais->count(),
                $federacao->locais->where('outro_modelo', true)->count()
            );

            return [
                'ultimo_formulario' => $formulario ? $formulario->ano_referencia : 'Sem Resposta',
                'total_umps_organizada' => $totalUmpsOrganizada,
                'total_igrejas_n_sociedades' => $totalIgrejasNSociedades
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
                $utlimoFormulario = $local->relatorios()
                    ->orderBy('created_at','desc')
                    ->get()
                    ->first();

                $ultimoAno = 'Sem Resposta';
                $total_socios = 0;
                if (!is_null($utlimoFormulario)) {
                    $total_socios = intval($utlimoFormulario->perfil['ativos'] ?? 0)
                        + intval($utlimoFormulario->perfil['cooperadores'] ?? 0);
                    $ultimoAno = $utlimoFormulario->ano_referencia;
                }


                $info_local[] = [
                    'id' => $local->id,
                    'nome' => $local->nome,
                    'status' => $local->status,
                    'numero_socios' => $total_socios,
                    'ultimo_formulario' => $ultimoAno
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
