<?php

namespace App\Services\Instancias;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\FormularioSinodalService;
use App\Services\LogErroService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SinodalService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $sinodal = Sinodal::create([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'regiao_id' => $request->regiao_id,
                'status' => $request->status == 'A' ? true : false
            ]);


            if ($request->has('email_usuario')) {
                $usuario = UserService::usuarioVinculado($request, $sinodal, 'sinodal', 'sinodais');
                if ($request->has('resetar_senha')) {
                    UserService::resetarSenha($usuario);
                }
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

    public static function update(Sinodal $sinodal, Request $request)
    {
        DB::beginTransaction();
        try {
            $sinodal->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'regiao_id' => $request->regiao_id,
                'status' => $request->status == 'A' ? true : false
            ]);

            if ($request->has('email_usuario')) {
                $usuario = UserService::usuarioVinculado($request, $sinodal, 'sinodal', 'sinodais');
                if ($request->has('resetar_senha')) {
                    UserService::resetarSenha($usuario);
                }
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


    public static function updateInfo(Sinodal $sinodal, Request $request)
    {
        DB::beginTransaction();

        try {
            $dataOrganizacao = null;
            
            if ($request->filled('data_organizacao')) {
                $dataOrganizacao = Carbon::createFromFormat('d/m/Y', $request->data_organizacao)->format('Y-m-d');
            }

            $sinodal->update([
                'nome' => $request->nome,
                'sinodo' => $request->sinodo,
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
    public static function getEstados()
    {
        try {
            $usuario = User::find(Auth::id());
            $regioes = Estado::whereIn('regiao_id', $usuario->regioes->pluck('id'))
                ->get()
                ->pluck('nome', 'id');
            return $regioes;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function getTotalizadores()
    {
        try {
            $sinodal = auth()->user()->sinodais->first();
            $federacoes = Federacao::where('sinodal_id', $sinodal->id)->get();
            $umps = Local::whereIn('federacao_id', $federacoes->pluck('id'))->get();
            $anoReferencia = EstatisticaService::getAnoReferencia();
            $formularios = FormularioFederacao::whereIn('federacao_id', $federacoes->pluck('id'))
                ->where('ano_referencia', $anoReferencia)
                ->get();
            if (!$formularios) {
                return [
                    'total_presbiterios' => $federacoes->count(),
                    'total_igrejas' => $umps->count(),
                    'total_n_sociedades_internas' => $umps->where('outro_modelo', true)->count(),
                    'total_federacoes' => $federacoes->where('status', true)->count(),
                    'total_umps' => $umps->where('status', true)->count(),
                    'total_socios' => 0,
                ];
            }
            $total_socios = 0;
            $total_umps = 0;
            foreach ($formularios as $formulario) {
                $total_umps += isset($formulario->estrutura) ? intval($formulario->estrutura['ump_organizada']) : 0;
                $total_socios += intval($formulario->perfil['ativos']) + intval($formulario->perfil['cooperadores']);
            }
            return [
                'total_presbiterios' => $federacoes->count(),
                'total_igrejas' => $umps->count(),
                'total_n_sociedades_internas' => $umps->where('outro_modelo', true)->count(),
                'total_federacoes' => $federacoes->where('status', true)->count(),
                'total_umps' => ($total_umps == 0 && $umps->where('status', true)->count() > 0)
                    ? $umps->where('status', true)->count()
                    : $total_umps,
                'total_socios' => $total_socios
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getInfo()
    {
        try {
            return Auth::user()->sinodais->first();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function delete(Sinodal $sinodal)
    {
        DB::beginTransaction();
        try {
            $sinodal->usuario->first()->update([
                'email' => 'apagadoComASinodalEm'.date('dmyhms').'@apagado.com'
            ]);
            $usuario = $sinodal->usuario->first();
            $sinodal->usuario()->sync([]);
            $usuario->delete();
            $sinodal->delete();
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

    public static function getInformacoesOrganizacao(Sinodal $sinodal) : array
    {
        try {
            $anoReferencia = EstatisticaService::getAnoReferencia();
            $formulario = FormularioSinodal::where('sinodal_id', $sinodal->id)
                ->where('ano_referencia', $anoReferencia)
                ->first();

            $totalUmpsOrganizada = self::getTotalUmpsOrganizadas($sinodal, $formulario);
            $totalFederacoesOrganizada = self::getTotalFederacoesOrganizadas($sinodal, $formulario);

            $totalUmpsOrganizadasString = "{$totalUmpsOrganizada['organizadas']} / {$totalUmpsOrganizada['total']}";
            $totalFederacoesOrganizadasString = $totalFederacoesOrganizada['organizadas']
                . "/"
                . $totalFederacoesOrganizada['total'];

            $totalUmpsOrganizada = self::getPorcentagem(
                $totalUmpsOrganizada['total'],
                $totalUmpsOrganizada['organizadas']
            );
            $totalFederacoesOrganizada = self::getPorcentagem(
                $totalFederacoesOrganizada['total'],
                $totalFederacoesOrganizada['organizadas']
            );
            $totalLocais = $sinodal->locais->count();
            $totalOutroModelo = $sinodal->locais->where('outro_modelo', true)->count();
            $totalIgrejasNSociedades = self::getPorcentagem(
                $totalLocais,
                $totalOutroModelo
            );

            $totalNSociedadesString = $totalOutroModelo
                . "/"
                . $totalLocais;

            return [
                'total_umps_organizada' => $totalUmpsOrganizada,
                'total_federacoes_organizada' => $totalFederacoesOrganizada,
                'total_igrejas_n_sociedades' => $totalIgrejasNSociedades,
                'total_umps_detalhe' => $totalUmpsOrganizadasString,
                'total_federacoes_detalhe' => $totalFederacoesOrganizadasString,
                'total_n_si_detalhe' => $totalNSociedadesString
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

    public static function getTotalUmpsOrganizadas(Sinodal $sinodal, FormularioSinodal $formulario = null) : array
    {
        if (!is_null($formulario)) {
            $total = ($formulario->estrutura['ump_organizada'] ?? 0)
                + ($formulario->estrutura['ump_nao_organizada'] ?? 0);
            return [
                'total' => $total,
                'organizadas' => $formulario->estrutura['ump_organizada'] ?? 0
            ];
        }
        return [
            'total' => $sinodal->locais->count(),
            'organizadas' => $sinodal->locais->where('status', true)->count()
        ];
    }
    public static function getTotalFederacoesOrganizadas(Sinodal $sinodal, FormularioSinodal $formulario = null) : array
    {
        if (!is_null($formulario)) {
            $total = ($formulario->estrutura['federacao_organizada'] ?? 0)
                + ($formulario->estrutura['federacao_nao_organizada'] ?? 0);
            return [
                'total' => $total,
                'organizadas' => $formulario->estrutura['federacao_organizada'] ?? 0
            ];
        }
        return [
            'total' => $sinodal->federacoes->count(),
            'organizadas' => $sinodal->federacoes->where('status', true)->count()
        ];
    }

    public static function getPorcentagem($total, $valor)
    {
        if ($total == 0) {
            return 0;
        }
        $resultado = ($valor * 100) / $total;
        return floatval(number_format($resultado, 2));
    }

    public static function getInformacoesFederacoesShow(Sinodal $sinodal) : array
    {
        try {
            $federacoes = $sinodal->federacoes;
            $infoFederacao = [];

            $anoReferencia = EstatisticaService::getAnoReferencia();
            foreach ($federacoes as $federacao) {
                $formulario = FormularioFederacao::where('federacao_id', $federacao->id)
                    ->where('ano_referencia', $anoReferencia)
                    ->first();
                $totalUmpsOrganizada = FederacaoService::getTotalUmpsOrganizadas($federacao, $formulario);

                $totalSocios = 0;
                if (!is_null($formulario)) {
                    $totalSocios = intval($formulario->perfil['ativos'] ?? 0)
                        + intval($formulario->perfil['cooperadores'] ?? 0);
                }

                $usuario = $federacao->usuario->first();

                $infoFederacao[] = [
                    'id' => $federacao->id,
                    'nome' => $federacao->nome,
                    'sigla' => $federacao->sigla,
                    'numero_umps' => $totalUmpsOrganizada['organizadas'] . ' de ' . $totalUmpsOrganizada['total'],
                    'numero_socios' => $totalSocios,
                    'status' => $federacao->status,
                    'usuario' => $usuario->email,
                    'usuarioId' => $usuario->id,
                    'origemRelatorio' => $totalUmpsOrganizada['relatorio']
                ];
            }
            return $infoFederacao;

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
     * Retorna a lista de sinodais em sessão vinculadas ao usuário
     *
     * @return array
     */
    public static function getListaSinodais(): array
    {
        if (session()->has('lista_sinodais')) {
            return session()->get('lista_sinodais');
        }
        $sinodais = Sinodal::select(['id', 'nome']);
        if (!auth()->user()->admin) {
            $sinodais = $sinodais->whereIn('regiao_id', auth()->user()->regioes->pluck('id')->toArray());
        }
        $listaSinodais = $sinodais->orderBy('nome')->get()->pluck('id')->toArray();

        session()->put('lista_sinodais', $listaSinodais);
        return $listaSinodais;
    }

    /**
     * Retorna o id da próxima sinodal e da anterior para navegação na tela de detalhes
     *
     * @param string $sinodalAtual
     * @return array
     */
    public static function navegacaoListaSinodais(string $sinodalAtual): array
    {
        $listaSinodais = self::getListaSinodais();
        $chave = array_search($sinodalAtual, $listaSinodais);

        return [
            'anterior' => $chave-1 < 0 ? null : $listaSinodais[$chave-1],
            'proxima' => isset($listaSinodais[$chave+1]) ? $listaSinodais[$chave+1] : null
        ];
    }


}
