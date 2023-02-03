<?php

namespace App\Services\Formularios;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\EstatisticaService;
use App\Services\LogErroService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormularioFederacaoService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $totalizador = self::totalizador($request->federacao_id);
            $programacoes = array_map(function($item) {
                return intval($item);
            }, $request->programacoes);
            $estrutura = array_map(function($item) {
                return intval($item);
            }, $request->estrutura);
            $formulario = FormularioFederacao::updateOrCreate(
                [
                    'ano_referencia' => Parametro::where('nome', 'ano_referencia')->first()->valor,
                    'federacao_id' => $request->federacao_id
                ],[
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'programacoes_locais' => $totalizador['programacoes'],
                'programacoes' => $programacoes,
                'aci' => $request->aci,
                'ano_referencia' => Parametro::where('nome', 'ano_referencia')->first()->valor,
                'federacao_id' => $request->federacao_id,
                'estrutura' => $estrutura
            ]);
            EstatisticaService::atualizarRelatorioGeral();
            AtualizarAutomaticamenteFormulariosService::atualizarSinodal($formulario);
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

    public static function delete(FormularioFederacao $formulario)
    {
        try {
            $formulario->delete();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Atualizar");

        }
    }

    public static function showFormulario($id)
    {
        try {
            $formulario = FormularioFederacao::find($id);
            $resumo = GraficoFormularioService::formatarResumo($formulario);
            $grafico = GraficoFormularioService::formatarGrafico($formulario);
            return [
                'resumo' => $resumo,
                'grafico' => $grafico
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function verificarColeta()
    {
        try {
            return Parametro::where('nome', 'coleta_dados')->first()->valor == 'SIM';
        } catch (\Throwable $th) {
            throw new Exception("Erro ao Verificar Coleta");
        }
    }



    public static function getAnosFormulariosRespondidos()
    {
        try {
            return $formularios = FormularioFederacao::whereIn('federacao_id', Auth::user()->federacoes->pluck('id'))
                ->get()
                ->pluck('ano_referencia', 'id');
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }

    public static function totalizador($id)
    {
        $locais = Local::where('federacao_id', $id)->get()->pluck('id');
        try {
            $formularios = FormularioLocal::whereIn('local_id', $locais)->where('ano_referencia', self::getAnoReferencia())->get();

            $totalizador = [
                'total_formularios' => $formularios->count(),
                'aci' => 0,
                'perfil' => [
                    'ativos' => 0,
                    'cooperadores' => 0,
                    'homens' => 0,
                    'mulheres' => 0,
                    'menor19' => 0,
                    'de19a23' => 0,
                    'de24a29' => 0,
                    'de30a35' => 0
                ],
                'escolaridade' => [
                    'fundamental' => 0,
                    'medio' => 0,
                    'tecnico' => 0,
                    'superior' => 0,
                    'pos' => 0,
                    'desempregado' => 0,
                ],
                'estado_civil' => [
                    'solteiros' => 0,
                    'casados' => 0,
                    'divorciados' => 0,
                    'viuvos' => 0,
                    'filhos' => 0,
                ],
                'deficiencias' => [
                    'surdos' => 0,
                    'auditiva' => 0,
                    'cegos' => 0,
                    'baixa_visao' => 0,
                    'fisica_inferior' => 0,
                    'fisica_superior' => 0,
                    'neurologico' => 0,
                    'intelectual' => 0,
                ],
                'programacoes' => [
                    'social' => 0,
                    'oracao' => 0,
                    'evangelistica' => 0,
                    'espiritual' => 0,
                    'recreativo' => 0,
                ]
            ];

            foreach ($formularios as $formulario) {
                    $totalizador['aci'] += isset($formulario->aci['valor']) ? floatval($formulario->aci['valor']) : 0;
                    $totalizador['perfil']['ativos'] += (isset($formulario->perfil['ativos']) ? intval($formulario->perfil['ativos']) : 0);
                    $totalizador['perfil']['cooperadores'] += (isset($formulario->perfil['cooperadores']) ? intval($formulario->perfil['cooperadores']) : 0);
                    $totalizador['perfil']['homens'] += (isset($formulario->perfil['homens']) ? intval($formulario->perfil['homens']) : 0);
                    $totalizador['perfil']['mulheres'] += (isset($formulario->perfil['mulheres']) ? intval($formulario->perfil['mulheres']) : 0);
                    $totalizador['perfil']['menor19'] += (isset($formulario->perfil['menor19']) ? intval($formulario->perfil['menor19']) : 0);
                    $totalizador['perfil']['de19a23'] += (isset($formulario->perfil['de19a23']) ? intval($formulario->perfil['de19a23']) : 0);
                    $totalizador['perfil']['de24a29'] += (isset($formulario->perfil['de24a29']) ? intval($formulario->perfil['de24a29']) : 0);
                    $totalizador['perfil']['de30a35'] += (isset($formulario->perfil['de30a35']) ? intval($formulario->perfil['de30a35']) : 0);
                    $totalizador['escolaridade']['fundamental'] += (isset($formulario->escolaridade['fundamental']) ? intval($formulario->escolaridade['fundamental']) : 0);
                    $totalizador['escolaridade']['medio'] += (isset($formulario->escolaridade['medio']) ? intval($formulario->escolaridade['medio']) : 0);
                    $totalizador['escolaridade']['tecnico'] += (isset($formulario->escolaridade['tecnico']) ? intval($formulario->escolaridade['tecnico']) : 0);
                    $totalizador['escolaridade']['superior'] += (isset($formulario->escolaridade['superior']) ? intval($formulario->escolaridade['superior']) : 0);
                    $totalizador['escolaridade']['pos'] += (isset($formulario->escolaridade['pos']) ? intval($formulario->escolaridade['pos']) : 0);
                    $totalizador['escolaridade']['desempregado'] += (isset($formulario->escolaridade['desempregado']) ? intval($formulario->escolaridade['desempregado']) : 0);
                    $totalizador['estado_civil']['solteiros'] += (isset($formulario->estado_civil['solteiros']) ? intval($formulario->estado_civil['solteiros']) : 0);
                    $totalizador['estado_civil']['casados'] += (isset($formulario->estado_civil['casados']) ? intval($formulario->estado_civil['casados']) : 0);
                    $totalizador['estado_civil']['divorciados'] += (isset($formulario->estado_civil['divorciados']) ? intval($formulario->estado_civil['divorciados']) : 0);
                    $totalizador['estado_civil']['viuvos'] += (isset($formulario->estado_civil['viuvos']) ? intval($formulario->estado_civil['viuvos']) : 0);
                    $totalizador['estado_civil']['filhos'] += (isset($formulario->estado_civil['filhos']) ? intval($formulario->estado_civil['filhos']) : 0);
                    $totalizador['deficiencias']['surdos'] += (isset($formulario->deficiencias['surdos']) ? intval($formulario->deficiencias['surdos']) : 0);
                    $totalizador['deficiencias']['auditiva'] += (isset($formulario->deficiencias['auditiva']) ? intval($formulario->deficiencias['auditiva']) : 0);
                    $totalizador['deficiencias']['cegos'] += (isset($formulario->deficiencias['cegos']) ? intval($formulario->deficiencias['cegos']) : 0);
                    $totalizador['deficiencias']['baixa_visao'] += (isset($formulario->deficiencias['baixa_visao']) ? intval($formulario->deficiencias['baixa_visao']) : 0);
                    $totalizador['deficiencias']['fisica_inferior'] += (isset($formulario->deficiencias['fisica_inferior']) ? intval($formulario->deficiencias['fisica_inferior']) : 0);
                    $totalizador['deficiencias']['fisica_superior'] += (isset($formulario->deficiencias['fisica_superior']) ? intval($formulario->deficiencias['fisica_superior']) : 0);
                    $totalizador['deficiencias']['neurologico'] += (isset($formulario->deficiencias['neurologico']) ? intval($formulario->deficiencias['neurologico']) : 0);
                    $totalizador['deficiencias']['intelectual'] += (isset($formulario->deficiencias['intelectual']) ? intval($formulario->deficiencias['intelectual']) : 0);

                    $totalizador['programacoes']['social'] += (isset($formulario->programacoes['social']) ? intval($formulario->programacoes['social']) : 0);
                    $totalizador['programacoes']['oracao'] += (isset($formulario->programacoes['oracao']) ? intval($formulario->programacoes['oracao']) : 0);
                    $totalizador['programacoes']['evangelistica'] += (isset($formulario->programacoes['evangelistica']) ? intval($formulario->programacoes['evangelistica']) : 0);
                    $totalizador['programacoes']['espiritual'] += (isset($formulario->programacoes['espiritual']) ? intval($formulario->programacoes['espiritual']) : 0);
                    $totalizador['programacoes']['recreativo'] += (isset($formulario->programacoes['recreativo']) ? intval($formulario->programacoes['recreativo']) : 0);
            }
            return $totalizador;
        } catch (\Throwable $th) {
            throw new Exception("Erro no Totalizador", 1);

        }
    }


    public static function getAnoReferencia() : int
    {
        try {
            return Parametro::where('nome', 'ano_referencia')->first()->valor;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function qualidadeEntrega() : array
    {

        try {
            $locais = Auth::user()->federacoes->first()->locais;
            $quantidade_entregue  = FormularioLocal::whereIn('local_id', $locais->pluck('id'))
                ->where('ano_referencia', self::getAnoReferencia())
                ->count();

            if ($quantidade_entregue == 0 && $locais->where('status', 1)->count() == 0) {
                $porcentagem = 0;
            } else {
                $porcentagem = round(($quantidade_entregue * 100) / $locais->where('status', 1)->count(), 2);
            }

            $data = ['porcentagem' => $porcentagem];
            if ($porcentagem < 50) {
                $data['color'] = 'danger';
                $data['texto'] = 'Quantidade Ruim (Tenha ao Menos 50%)';
            } else if ($porcentagem >= 50 && $porcentagem <= 75) {
                $data['color'] = 'Quantidade Mediana, mas pode melhorar';
                $data['texto'] = 'Ainda não é o ideal, mas você já pode enviar e/ou atualizar depois';
            } else {
                $data['color'] = 'success';
                $data['texto'] = 'Quantidade mínima Ideal';
            }
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getEstrutura() : array
    {

        try {
            $locais = Auth::user()->federacoes->first()->locais;
            $formularios_entregue  = FormularioLocal::whereIn('local_id', $locais->pluck('id'))
                ->where('ano_referencia', self::getAnoReferencia())
                ->get();
            $data = [
                'quantidade_umps' => $locais->where('status', 1)->count(),
                'quantidade_sem_ump' => $locais->where('status', 0)->count(),
                'nro_repasse' => $formularios_entregue->where('aci.repasse', 'S')->count(),
                'nro_sem_repasse' => $formularios_entregue->where('aci.repasse', 'N')->count()
            ];

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getFormularioAnoCorrente()
    {
        return FormularioFederacao::where('federacao_id', Auth::user()->federacoes->first()->id)
            ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
            ->first();
    }

    public static function getFormulario($ano) : ?FormularioFederacao
    {
        return FormularioFederacao::where('federacao_id', Auth::user()->federacoes->first()->id)
            ->where('ano_referencia', $ano)
            ->first();
    }

    public static function getFormularioDaFederacao($federacao) : ?FormularioFederacao
    {
        return FormularioFederacao::where('federacao_id', $federacao)
            ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
            ->first();
    }
}
