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


            $usuario = UserService::usuarioVinculado(
                $request,
                $federacao,
                'federacao',
                'federacao_id'
            );

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

            $usuario = UserService::usuarioVinculado(
                $request,
                $federacao,
                'federacao',
                'federacao_id'
            );

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
        $regiao = auth()->user()->sinodal->regiao->id;
        $estados = Estado::where('regiao_id', $regiao)
            ->get()
            ->pluck('nome', 'id');
        return $estados;
    }

    public static function getSinodal()
    {
        return Sinodal::where('regiao_id', auth()->user()->regiao_id)
            ->get()
            ->pluck('nome', 'id');
    }


    public static function updateInfo(Federacao $federacao, Request $request)
    {
        DB::beginTransaction();

        try {
            $dataOrganizacao = null;
            
            if ($request->filled('data_organizacao')) {
                $dataOrganizacao = Carbon::createFromFormat('d/m/Y', $request->data_organizacao)->format('Y-m-d');
            }

            $federacao->update([
                'nome' => $request->nome,
                'presbiterio' => $request->presbiterio,
                'data_organizacao' => $dataOrganizacao,
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
        return auth()->user()->federacao;
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
                    'total_socios' => 'Resposta Pendente',
                ];
            }
            return [
                'total_umps' => $federacao->locais->count() ?? 0,
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
            if (!empty($federacao->usuario)) {
                $federacao->usuario->update([
                    'email' => 'apagadoFedEm'.date('dmyhms').'@apagado.com',
                    'federacao_id' => null
                ]);
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
                'total' => $total ?: $federacao->locais->count(),
                'organizadas' => $formulario->estrutura['ump_organizada'] ?? $federacao->locais->count(),
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

    public static function getInformacoesLocaisShow(Federacao $federacao): array
    {
        try {

            $locais = $federacao->locais()
                ->orderBy('status', 'desc')
                ->get();
            $infoLocal = [];
            foreach ($locais as $local) {
                $utlimoFormulario = $local->relatorios()
                    ->orderBy('created_at','desc')
                    ->get()
                    ->first();

                $ultimoAno = 'Sem Resposta';
                $ultimaACI = 'Sem Resposta';
                $totalSocio = 0;
                $anoReferencia = EstatisticaService::getAnoReferencia();
                $mesmoAno = false;

                if (!is_null($utlimoFormulario)) {
                    $totalSocio = intval($utlimoFormulario->perfil['ativos'] ?? 0)
                        + intval($utlimoFormulario->perfil['cooperadores'] ?? 0);
                    $ultimoAno = $utlimoFormulario->ano_referencia;
                    $ultimaACI = $utlimoFormulario->aci['repasse'] == 'S'
                        ? "R$ {$utlimoFormulario->aci['valor']}"
                        : 'Sem Repasse';
                    $mesmoAno = $utlimoFormulario->ano_referencia == $anoReferencia;
                }
                $temDiretoria = $local->diretoria ? true : false;
                $infoLocal[] = [
                    'id' => $local->id,
                    'nome' => $local->nome,
                    'status' => $local->status,
                    'numeroSocios' => $totalSocio,
                    'ultimoFormulario' => $ultimoAno,
                    'temDiretoria' => $temDiretoria,
                    'ultimaAtualizacaoDiretoria' => $temDiretoria
                        ? $local->diretoria->updated_at->format('d/m/Y')
                        : 'Sem Diretoria',
                    'ultimaACI' => $ultimaACI,
                    'diretoria' => $temDiretoria
                        ? json_encode(DiretoriaService::getDiretoriaTabela($local->id, DiretoriaService::TIPO_DIRETORIA_LOCAL))
                        : '',
                    'mesmoAno' => $mesmoAno
                ];
            }
            return $infoLocal;

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
            $federacoes = $federacoes->where('sinodal_id', auth()->user()->sinodal_id);
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
