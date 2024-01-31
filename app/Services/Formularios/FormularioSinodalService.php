<?php

namespace App\Services\Formularios;

use App\Helpers\FormHelper;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioSinodal;
use App\Models\Parametro;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\Totalizadores\TotalizadorFormularioSinodalService;
use App\Services\LogErroService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormularioSinodalService
{

    /**
     * Método responsável por salvar o formulário estatística da sinodal
     * Caso o parâmetro apenasSalvar seja verdadeiro o status será FORMULARIO_RESPOSTA_PARCIAL
     * e não será contabilizado como entregue
     *
     * @param Request $request
     * @param boolean $apenasSalvar
     * @return void
     */
    public static function store(Request $request, bool $apenasSalvar = false)
    {
        try {

            // VALIDAÇÃO
            $anoReferencia = EstatisticaService::getAnoReferencia();
            $porcentagem = EstatisticaService::getValorPorcentagemEntregaFormularioFederacao(
                $request->sinodal_id,
                $anoReferencia
            );

            if  (
                !$apenasSalvar
                && $porcentagem < EstatisticaService::getPorcentagemMinimaEntrega('sinodal')
            ) {
                throw new Exception(
                    "Sinodal está tentando burlar o sistema ao submter um formulário sem a quantidade mínima",
                    500
                );
            }
            $programacoes = array_map(function($item) {
                return intval($item);
            }, $request->programacoes);
            $estrutura = array_map(function($item) {
                return intval($item);
            }, $request->estrutura);

            $totalizador = TotalizadorFormularioSinodalService::totalizador($request->sinodal_id);

            FormularioSinodal::updateOrCreate(
                [
                    'ano_referencia' => $anoReferencia,
                    'sinodal_id' => $request->sinodal_id
                ],
                [
                    'perfil' => $totalizador['perfil'],
                    'estado_civil' => $totalizador['estado_civil'],
                    'escolaridade' => $totalizador['escolaridade'],
                    'deficiencias' => $totalizador['deficiencias'],
                    'estrutura' => $estrutura,
                    'programacoes_federacoes' => $totalizador['programacoes_federacao'],
                    'programacoes_locais' => $totalizador['programacoes_locais'],
                    'programacoes' => $programacoes,
                    'aci' => $request->aci,
                    'ano_referencia' => $anoReferencia,
                    'sinodal_id' => $request->sinodal_id,
                    'status' => $apenasSalvar
                        ? EstatisticaService::FORMULARIO_RESPOSTA_PARCIAL
                        : EstatisticaService::FORMULARIO_ENTREGUE
                ]
            );

            EstatisticaService::atualizarRelatorioGeral();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Salvar");
        }
    }

    public static function delete(FormularioSinodal $formulario)
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

    public static function verificarColeta()
    {
        try {
            $parametro_ativo = Parametro::where('nome', 'coleta_dados')->first()->valor == 'SIM';
            return $parametro_ativo;
        } catch (\Throwable $th) {
            throw new Exception("Erro ao Verificar Coleta");
        }
    }

    /**
     * Retorna o formulário da sinodal do ano corrente
     *
     * @return FormularioSinodal|null
     */
    public static function getFormularioAnoCorrente(): ?FormularioSinodal
    {
        return FormularioSinodal::where('sinodal_id', Auth::user()->sinodais->first()->id)
            ->where('ano_referencia', EstatisticaService::getAnoReferencia())
            ->first();
    }

    public static function showFormulario($id)
    {
        try {
            $formulario = FormularioSinodal::find($id);
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

    public static function getAnosFormulariosRespondidos()
    {
        try {
            return FormularioSinodal::whereIn('sinodal_id', auth()->user()->sinodais->pluck('id'))
                ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
                ->get()
                ->pluck('ano_referencia', 'id');
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }



    public static function qualidadeEntrega() : array
    {

        try {
            $federacoes = auth()->user()->sinodais->first()
                ->federacoes()
                ->where('status', true)
                ->get();
            $quantidade_entregue  = FormularioFederacao::whereIn('federacao_id', $federacoes->pluck('id'))
                ->where('ano_referencia', EstatisticaService::getAnoReferencia())
                ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
                ->count();

            $quantidadeFederacoesAtivas = $federacoes->where('status', 1)->count();
            if ($quantidade_entregue == 0 && $quantidadeFederacoesAtivas == 0) {
                $porcentagem = 0;
            } elseif ($quantidade_entregue != 0 && $quantidadeFederacoesAtivas == 0) {
                // nos casos de federações não organizadas que o vice conseguiu que preenchessem o formulário
                $quantidadeFederacoes = $federacoes->count() ?: 1;
                $porcentagem = round(($quantidade_entregue * 100) / $quantidadeFederacoes , 2);
            } else {
                $porcentagem = round(($quantidade_entregue * 100) / $quantidadeFederacoesAtivas , 2);
            }

            $data = ['porcentagem' => $porcentagem];
            $porcentagem_min = (float) Parametro::where('nome', 'min_sinodal')->first()->valor;
            $data['minimo'] = $porcentagem_min;
            if ($porcentagem < $porcentagem_min) {
                $data['color'] = 'danger';
                $data['texto'] = "Quantidade Ruim (Tenha ao menos {$porcentagem_min}%)";
            } else {
                $data['color'] = 'success';
                $data['texto'] = 'Quantidade mínima alcançada';
            }
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getEstrutura() : array
    {

        try {
            $federacoes = Auth::user()->sinodais->first()->federacoes;
            $formularios_entregue  = FormularioFederacao::whereIn('federacao_id', $federacoes->pluck('id'))
                ->where('ano_referencia', EstatisticaService::getAnoReferencia())
                ->get();

            $totalizador = self::totalizador(Auth::user()->sinodais->first()->id);

            $data = [
                'quantidade_federacoes' => $federacoes->where('status', 1)->count(),
                'quantidade_sem_federacao' => $federacoes->where('status', 0)->count(),
                'quantidade_ump' => $totalizador['estrutura']['ump_organizada'],
                'quantidade_sem_ump' => $totalizador['estrutura']['ump_nao_organizada'],
                'quantidade_ump_repasse' => $totalizador['estrutura']['nro_repasse'],
                'quantidade_ump_sem_repasse' => $totalizador['estrutura']['nro_sem_repasse'],
                'federacao_nro_repasse' => $formularios_entregue->where('aci.repasse', 'S')->count(),
                'federacao_nro_sem_repasse' => $formularios_entregue->where('aci.repasse', 'N')->count()
            ];

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function totalizador($id)
    {
        $federacoes = Federacao::where('sinodal_id', $id)->get()->pluck('id');
        try {
            $formularios = FormularioFederacao::whereIn('federacao_id', $federacoes)
                ->where('ano_referencia', EstatisticaService::getAnoReferencia())
                ->get();

            $totalizador = [
                'total_formularios' => $formularios->count(),
                'aci' => 0,
                'estrutura' => [
                    'ump_organizada' => 0,
                    'ump_nao_organizada' => 0,
                    'nro_repasse' => 0,
                    'nro_sem_repasse' => 0
                ],
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
                'programacoes_federacoes' => [
                    'social' => 0,
                    'oracao' => 0,
                    'evangelistica' => 0,
                    'espiritual' => 0,
                    'recreativo' => 0,
                ],
                'programacoes_locais' => [
                    'social' => 0,
                    'oracao' => 0,
                    'evangelistica' => 0,
                    'espiritual' => 0,
                    'recreativo' => 0,
                ]
            ];

            foreach ($formularios as $formulario) {
                $totalizador['aci'] += isset($formulario->aci['valor']) ? FormHelper::converterParaFloat($formulario->aci['valor']) : 0;
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

                $totalizador['estrutura']['ump_organizada'] += (isset($formulario->estrutura['ump_organizada']) ? intval($formulario->estrutura['ump_organizada']) : 0);
                $totalizador['estrutura']['ump_nao_organizada'] += (isset($formulario->estrutura['ump_nao_organizada']) ? intval($formulario->estrutura['ump_nao_organizada']) : 0);
                $totalizador['estrutura']['nro_repasse'] += (isset($formulario->estrutura['nro_repasse']) ? intval($formulario->estrutura['nro_repasse']) : 0);
                $totalizador['estrutura']['nro_sem_repasse'] += (isset($formulario->estrutura['nro_sem_repasse']) ? intval($formulario->estrutura['nro_sem_repasse']) : 0);

                $totalizador['programacoes_federacoes']['social'] += (isset($formulario->programacoes['social']) ? intval($formulario->programacoes['social']) : 0);
                $totalizador['programacoes_federacoes']['oracao'] += (isset($formulario->programacoes['oracao']) ? intval($formulario->programacoes['oracao']) : 0);
                $totalizador['programacoes_federacoes']['evangelistica'] += (isset($formulario->programacoes['evangelistica']) ? intval($formulario->programacoes['evangelistica']) : 0);
                $totalizador['programacoes_federacoes']['espiritual'] += (isset($formulario->programacoes['espiritual']) ? intval($formulario->programacoes['espiritual']) : 0);
                $totalizador['programacoes_federacoes']['recreativo'] += (isset($formulario->programacoes['recreativo']) ? intval($formulario->programacoes['recreativo']) : 0);

                $totalizador['programacoes_locais']['social'] += (isset($formulario->programacoes_locais['social']) ? intval($formulario->programacoes_locais['social']) : 0);
                $totalizador['programacoes_locais']['oracao'] += (isset($formulario->programacoes_locais['oracao']) ? intval($formulario->programacoes_locais['oracao']) : 0);
                $totalizador['programacoes_locais']['evangelistica'] += (isset($formulario->programacoes_locais['evangelistica']) ? intval($formulario->programacoes_locais['evangelistica']) : 0);
                $totalizador['programacoes_locais']['espiritual'] += (isset($formulario->programacoes_locais['espiritual']) ? intval($formulario->programacoes_locais['espiritual']) : 0);
                $totalizador['programacoes_locais']['recreativo'] += (isset($formulario->programacoes_locais['recreativo']) ? intval($formulario->programacoes_locais['recreativo']) : 0);
            }
            return $totalizador;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro no Totalizador", 1);

        }
    }

    public static function getFormularioDaSinodal($sinodal) : ?FormularioSinodal
    {
        return FormularioSinodal::where('sinodal_id', $sinodal)
            ->where('ano_referencia', EstatisticaService::getAnoReferencia())
            ->first();
    }

    public static function getFormulario($ano)
    {
        return FormularioSinodal::where('sinodal_id', auth()->user()->sinodais->first()->id)
            ->where('ano_referencia', $ano)
            ->first();
    }

    public static function getFederacoes()
    {
        try {
            $federacoes = Auth::user()->sinodais->pluck('id');
            return Federacao::whereIn('sinodal_id', $federacoes)->get()->map(function($federacao) {
                return ['id' => $federacao->id, 'text' => $federacao->sigla];
            });
        } catch (\Throwable $th) {
            throw new Exception("Error durante a busca de estados", 1);
        }
    }
}
