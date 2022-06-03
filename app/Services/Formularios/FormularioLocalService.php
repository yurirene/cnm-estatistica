<?php

namespace App\Services\Formularios;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioLocal;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FormularioLocalService
{

    public static function store(Request $request)
    {
        try {
            FormularioLocal::create([
                'perfil' => $request->perfil,
                'estado_civil' => $request->estado_civil,
                'escolaridade' => $request->escolaridade,
                'deficiencias' => $request->deficiencia,
                'programacoes' => $request->programacoes,
                'aci' => $request->aci,
                'ano_referencia' => date('Y'),
                'local_id' => $request->local_id
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Salvar");           
        }
    }

    public static function delete(FormularioLocal $formulario)
    {
        try {
            $formulario->delete();
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Atualizar");
            
        }
    }

    public static function verificarColeta()
    {
        try {
            $parametro_ativo = Parametro::where('nome', 'coleta_dados')->first()->valor == 'SIM';
            $existe_formulario = FormularioLocal::where('local_id', Auth::user()->locais->first()->id)
                ->where('ano_referencia', date('Y'))
                ->get()
                ->isEmpty();
            return $existe_formulario && $parametro_ativo;
        } catch (\Throwable $th) {
            throw new Exception("Erro ao Verificar Coleta");
        }
    }

    public static function showFormulario($id)
    {
        try {
            $formulario = FormularioLocal::find($id);
            $resumo = self::formatarResumo($formulario);
            $grafico = self::formatarGrafico($formulario);
            return [
                'resumo' => $resumo,
                'grafico' => $grafico
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function formatarResumo($dados)
    {
        return [
            'ano_referencia' => $dados->ano_referencia,
            'aci' => ($dados->aci['repasse'] == 'S' ? 'Repassado ' . $dados->aci['valor'] : 'Não repassado' ),
            'ativos' => $dados->perfil['ativos'] ?? 0,
            'cooperadores' => $dados->perfil['cooperadores'] ?? 0,
            'homens' => $dados->perfil['homens'] ?? 0,
            'mulheres' => $dados->perfil['mulheres'] ?? 0,
            'menor19' => $dados->perfil['menor19'] ?? 0,
            'de19a23' => $dados->perfil['de19a23'] ?? 0,
            'de24a29' => $dados->perfil['de24a29'] ?? 0,
            'de30a35' => $dados->perfil['de30a35'] ?? 0,
            'fundamental' => $dados->escolaridade['fundamental'] ?? 0,
            'medio' => $dados->escolaridade['medio'] ?? 0,
            'tecnico' => $dados->escolaridade['tecnico'] ?? 0,
            'superior' => $dados->escolaridade['superior'] ?? 0,
            'pos' => $dados->escolaridade['pos'] ?? 0,
            'desempregado' => $dados->escolaridade['desempregado'] ?? 0,
            'solteiros' => $dados->estado_civil['solteiros'] ?? 0,
            'casados' => $dados->estado_civil['casados'] ?? 0,
            'divorciados' => $dados->estado_civil['divorciados'] ?? 0,
            'viuvos' => $dados->estado_civil['viuvos'] ?? 0,
            'filhos' => $dados->estado_civil['filhos'] ?? 0,
            'surdos' => $dados->deficiencias['surdos'] ?? 0,
            'auditiva' => $dados->deficiencias['auditiva'] ?? 0,
            'cegos' => $dados->deficiencias['cegos'] ?? 0,
            'baixa_visao' => $dados->deficiencias['baixa_visao'] ?? 0,
            'fisica_inferior' => $dados->deficiencias['fisica_inferior'] ?? 0,
            'fisica_superior' => $dados->deficiencias['fisica_superior'] ?? 0,
            'neurologico' => $dados->deficiencias['neurologico'] ?? 0,
            'intelectual' => $dados->deficiencias['intelectual'] ?? 0,
            'social' => $dados->programacoes['social'] ?? 0,
            'evangelistico' => $dados->programacoes['evangelistico'] ?? 0,
            'espiritual' => $dados->programacoes['espiritual'] ?? 0,
            'recreativo' => $dados->programacoes['recreativo'] ?? 0,
            'oracao' => $dados->programacoes['oracao'] ?? 0,
        ];
    }

    public static function formatarGrafico($dados)
    {
        $retorno = [
            'perfil' => [
                'labels' => ['Ativos', 'Cooperadores', 'Homens', 'Mulheres', 'Menor de 19', 'Entre 19 e 23', 'Entre 24 e 29', 'Entre 30 e 35'],
                'datasets' => [
                    [
                        'backgroundColor' => ['#003f5c', '#2f4b7c'],
                        'data' => self::processarDadosPorcentagem([
                            $dados->perfil['ativos'] ?? 0, 
                            $dados->perfil['cooperadores'] ?? 0
                        ])
                    ],
                    [
                        'backgroundColor' => ['#665191', '#a05195'],
                        'data' => self::processarDadosPorcentagem([
                            $dados->perfil['homens'] ?? 0, 
                            $dados->perfil['mulheres'] ?? 0
                        ])
                    ],
                    [
                        'backgroundColor' => ['#d45087', '#f95d6a', '#ff7c43', '#ffa600'],
                        'data' => self::processarDadosPorcentagem([
                            $dados->perfil['menor19'] ?? 0, 
                            $dados->perfil['de19a23'] ?? 0 , 
                            $dados->perfil['de24a29'] ?? 0 , 
                            $dados->perfil['de30a35'] ?? 0
                        ])
                    ],
                ]
            ],
            'programacoes' => [
                'labels' => ['Vigília e Oração', 'Social', 'Evangelistico/Missional', 'Espiritual', 'Recreativo' ],
                'datasets' => [
                    [
                        'data' => self::processarDadosPorcentagem([
                            $dados->programacoes['oracao'], 
                            $dados->programacoes['social'], 
                            $dados->programacoes['evangelistico'], 
                            $dados->programacoes['espiritual'], 
                            $dados->programacoes['recreativo']
                        ]),
                        'backgroundColor' => ['#003f5c','#58508d','#bc5090','#ff6361','#ffa600']
                    ]
                ]
            ],
            'escolaridade' => [
                'labels' => ['Ens. Fundamental', 'Ens. Médio', 'Ens. Técnico', 'Ens. Superior', 'Pós-Graduação' ],
                'datasets' => [
                    [
                        'data' => self::processarDadosPorcentagem([
                            $dados->escolaridade['fundamental'],
                            $dados->escolaridade['medio'],
                            $dados->escolaridade['tecnico'],
                            $dados->escolaridade['superior'],
                            $dados->escolaridade['pos']
                        ]),
                        'backgroundColor' => ['#003f5c','#58508d','#bc5090','#ff6361','#ffa600']
                    ]
                ]
            ]
        ];

        return $retorno;
    }

    public static function processarDadosPorcentagem(array $dados)
    {
        try {
            $total = 0;
            foreach($dados as $valor){
                $total += $valor;
            }
            
            $retorno = [];

            foreach ($dados as $valor) {
                $retorno[] = floatval(number_format(($valor * 100 / $total), 2));
            }
            return $retorno;
        } catch (\Throwable $th) {
            throw new Exception("Erro ao processar dados progração");
        }
    }

    

    public static function getAnosFormulariosRespondidos()
    {
        try {
            return $formularios = FormularioLocal::whereIn('local_id', Auth::user()->locais->pluck('id'))
                ->get()
                ->pluck('ano_referencia', 'id');
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }

}