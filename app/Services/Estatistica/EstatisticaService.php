<?php

namespace App\Services\Estatistica;

use App\Exports\BaseDadosFormularioExport;
use App\Models\EstatisticaGeral;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Ranking;
use App\Models\Sinodal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EstatisticaService
{

    public static function atualizarParametro(array $request) : void
    {
        $valor = $request['valor'];
        if (in_array($valor,['true', 'false'])) {
            $valor = $request['valor'] == 'true' ? 'SIM' : 'NAO';
        }
        try {
            $parametro = Parametro::find($request['id']);
            $parametro->update([
                'valor' => $valor
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getParametros() : Collection
    {
        try {
            return Parametro::get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'nome' => $item->nome,
                    'valor' => $item->valor,
                    'label' => $item->descricao,
                    'tipo' => $item->tipo
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getAnoReferenciaFormularios() : array
    {
        return FormularioSinodal::selectRaw('DISTINCT(ano_referencia) as ano_referencia')
        ->groupBy('ano_referencia')
        ->get()
        ->pluck('ano_referencia', 'ano_referencia')
        ->toArray();
    }

    public static function exportarExcel(array $request)
    {
        $formulario_base = FormularioSinodal::with(['sinodal', 'sinodal.regiao'])->where('ano_referencia', $request['ano_referencia'])->first()->toArray();
        $dados = collect($formulario_base)->except([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'sinodal_id',
            'sinodal.id',
            'sinodal.sigla',
            'sinodal.data_organizacao',
            'sinodal.sinodo',
            'sinodal.midias_sociais',
            'sinodal.regiao_id',
            'sinodal.status',
            'sinodal.created_at',
            'sinodal.updated_at',
            'sinodal.deleted_at'
        ]);
        $campos = [];
        $somente_colunas = [];
        foreach ($dados as $coluna_master => $coluna) {
            if (!is_array($coluna)) {
                $campos[] = $coluna_master;
                $somente_colunas[] = $coluna_master;
                continue;
            }
            $campos[$coluna_master] = array_keys($coluna);
            array_push($somente_colunas, ...array_keys($coluna));
        }
        return Excel::download(new BaseDadosFormularioExport($campos, $somente_colunas, $request['ano_referencia']), 'base_dados_' . date('d_m_Y') . '.xlsx');

    }

    /**
     * Retorna a collection com os ids das federações e se entregaram os relatórios
     */
    public static function getDadosFormularioFederacao(string $idSinodal, int $ano): Collection
    {
        try {
            return Federacao::where('sinodal_id', $idSinodal)
            ->where('status', true)
            ->get()
            ->map(function ($item) use ($ano) {
                return [
                    'id' => $item->id,
                    'formulario' => $item->relatorios()
                        ->where('ano_referencia', $ano)
                        ->get()
                        ->count(),
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retorna a porcentagem formatada da relação de formuários preenchidos das federações
     */
    public static function getPorcentagemFormularioFederacao(string $idSinodal, int $ano): string
    {
        try {
            $federacoes = self::getDadosFormularioFederacao($idSinodal, $ano);
            $formularios = $federacoes->where('formulario', '!=', 0)->count();
            $porcentagem = 0;
            if ($federacoes->count() != 0) {
                $porcentagem = round(($formularios * 100) / $federacoes->count(), 2);
            }
            return "{$porcentagem}% ({$formularios} de {$federacoes->count()})";
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retorna a collection com os ids das locais e se entregaram os relatórios
     */
    public static function getDadosFormularioLocal(string $idSinodal, int $ano): Collection
    {
        try {
            return Local::where('sinodal_id', $idSinodal)
            ->where('status', true)
            ->get()
            ->map(function ($item) use ($ano) {
                return [
                    'id' => $item->id,
                    'formulario' => $item->relatorios()
                        ->where('ano_referencia', $ano)
                        ->get()
                        ->count(),
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retorna a porcentagem formatada da relação de formuários preenchidos das locais
     */
    public static function getPorcentagemFormularioLocal($idSinodal, $ano): string
    {
        try {
            $locais = self::getDadosFormularioLocal($idSinodal, $ano);
            $formularios = $locais->where('formulario', '!=', 0)->count();
            $porcentagem = 0;
            if ($locais->count() != 0) {
                $porcentagem = round(($formularios * 100) / $locais->count(), 2);
            }
            return "{$porcentagem}% ({$formularios} de {$locais->count()})";
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Faz o calculo da qualidade do relatório, levando em consideração
     * somente os relatórios das locais que fazem parte das federações que entregaram
     */
    public static function calcularQualidade(string $sinodal, int $ano): int
    {
        try {
            $federacoes = Federacao::where('sinodal_id', $sinodal)
                ->whereHas('relatorios', function ($sql) use ($ano) {
                    return $sql->where('ano_referencia', $ano);
                })
                ->get()
                ->pluck('id');
            $total_locais = $locais = Local::where('sinodal_id', $sinodal)
                ->where('status', true)
                ->get()
                ->count();
            $locais = Local::where('status', true)
                ->whereIn('federacao_id', $federacoes)
                ->get()
                ->map(function ($item) use ($ano) {
                    return [
                        'id' => $item->id,
                        'formulario' => $item->relatorios()
                            ->where('ano_referencia', $ano)
                            ->get()
                            ->count(),
                    ];
                });
            $formularios = $locais->where('formulario', '!=', 0)->count();
            if ($locais->count() == 0) {
                return 0;
            }
            $qualidade = ($formularios * 100) / $total_locais;
            return ceil($qualidade) > 100 ? 100 : ceil($qualidade);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Lista os dados e orderna por qualidade salvando ou atualizando
     * posteriormente no banco de dados
     */
    public static function atualizarRanking()
    {
        try {
            $dados = self::getDadosQualidadeEstatistica()
                ->sortByDesc('qualidade')
                ->groupBy('qualidade');
            $posicao = 1;
            foreach ($dados as $dado) {
                foreach ($dado as $sinodal) {
                    Ranking::updateOrCreate([
                        'sinodal_id' => $sinodal['id'],
                        'ano_referencia' => $sinodal['ano']
                    ], [
                        'sinodal_id' => $sinodal['id'],
                        'pontuacao' => $sinodal['qualidade'],
                        'ano_referencia' => $sinodal['ano'],
                        'posicao' => $posicao,
                        'form_fed_entregue' => $sinodal['qtd_fomr_fed'],
                        'form_local_entregue' => $sinodal['qtd_fomr_local']
                    ]);
                }
                $posicao++;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retorna uma collection contendo os dados necessários para atulizar o ranking
     * e listar no dataTable de Relatórios Estatísticos
     */
    public static function getDadosQualidadeEstatistica(): Collection
    {
        $ano = Parametro::where('nome', 'ano_referencia')->first()->valor;
        return Sinodal::where('status', true)
            ->orderBy('regiao_id')
            ->orderBy('nome')
            ->get()
            ->map(function ($item) use ($ano) {
                $nome = str_replace('Sinodal', 'S', $item->nome);
                $nome = str_replace('Confederação', 'C', $nome);
                $nome = str_replace(['Mocidade', 'Mocidades'], 'M', $nome);
                return [
                    'id' => $item->id,
                    'ano' => $ano,
                    'nome' => $nome,
                    'entregue' =>  $item->relatorios()
                        ->where('ano_referencia', $ano)
                        ->get()
                        ->count(),
                    'federacoes' => self::getPorcentagemFormularioFederacao($item->id, $ano),
                    'locais' => self::getPorcentagemFormularioLocal($item->id, $ano),
                    'qtd_fomr_fed' => self::getDadosFormularioFederacao($item->id, $ano)->where('formulario', '!=', 0)->count(),
                    'qtd_fomr_local' => self::getDadosFormularioLocal($item->id, $ano)->where('formulario', '!=', 0)->count(),
                    'qualidade' => self::calcularQualidade($item->id, $ano),
                    'regiao' => $item->regiao->nome
                ];
            });
    }


    public static function getTotalizadores(): array
    {
        $ano_referencia = Parametro::where('nome', 'ano_referencia')->first()->valor;

        $dados = EstatisticaGeral::where('ano_referencia', $ano_referencia)->first();
        return [
            'total_sinodais' => $dados['estrutura']['sinodais_organizadas'],
            'total_federacoes' => $dados['estrutura']['federacoes_organizadas'],
            'total_umps' => $dados['estrutura']['umps_organizadas'],
            'total_socios' => $dados['perfil']['ativos'] + $dados['perfil']['cooperadores'],
            'relatorios_sinodais' => $dados['abrangencia']['sinodais']['respondido'] . ' / ' . $dados['abrangencia']['sinodais']['total'],
            'relatorios_federacoes' => $dados['abrangencia']['federacoes']['respondido'] . ' / ' . $dados['abrangencia']['federacoes']['total'],
            'relatorios_locais' => $dados['abrangencia']['locais']['respondido'] . ' / ' . $dados['abrangencia']['locais']['total'],
            'qualidade' => $dados['qualidade'],
        ];
    }


    /**
     * Função para atualizar o relatório geral do ano vigente
     *
     * @return void
     */
    public static function atualizarRelatorioGeral()
    {

        $ano_referencia = Parametro::where('nome', 'ano_referencia')->first()->valor;
        $totalizador = self::getDadosRelatorioGeral($ano_referencia);
        EstatisticaGeral::updateOrCreate(
            [
                'ano_referencia' => $ano_referencia,
            ],
            [
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'programacoes_locais' => $totalizador['programacoes']['locais'],
                'programacoes_federacoes' => $totalizador['programacoes']['federacoes'],
                'programacoes_sinodais' => $totalizador['programacoes']['sinodais'],
                'aci' => $totalizador['aci'],
                'estrutura' => $totalizador['estrutura'],
                'abrangencia' => $totalizador['abrangencia'],
                'qualidade' => $totalizador['qualidade']
            ]
        );
    }

    /**
     * Função que retorna os dados estatísticos gerais, independente de entrega
     *
     * @param [type] $ano_referencia
     * @return array
     */
    public static function getDadosRelatorioGeral($ano_referencia, $regiao_id = null): array
    {
        try {
            $formularios_locais = FormularioLocal::where('ano_referencia', $ano_referencia)
                ->when(!is_null($regiao_id), function ($sql) use ($regiao_id) {
                    return $sql->whereHas('local', function ($q) use ($regiao_id) {
                        return $q->where('regiao_id', $regiao_id);
                    });
                })
                ->get();
            $totalizador = [];

            $totalizador = [
                'aci' => [
                    'locais' => 0,
                    'locais_nao' => 0,
                    'federacoes' => 0,
                    'federacoes_nao' => 0,
                    'sinodais' => 0,
                    'sinodais_nao' => 0
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
                    'sinodais' => [
                        'social' => 0,
                        'oracao' => 0,
                        'evangelistico' => 0,
                        'espiritual' => 0,
                        'recreativo' => 0,
                    ],
                    'federacoes' => [
                        'social' => 0,
                        'oracao' => 0,
                        'evangelistico' => 0,
                        'espiritual' => 0,
                        'recreativo' => 0,
                    ],
                    'locais' => [
                        'social' => 0,
                        'oracao' => 0,
                        'evangelistico' => 0,
                        'espiritual' => 0,
                        'recreativo' => 0,
                    ]
                ]
            ];

            foreach ($formularios_locais as $formulario) {
                $totalizador['aci']['locais'] += isset($formulario->aci['valor']) && $formulario->aci['repasse'] == 'S' ? 1 : 0;
                $totalizador['aci']['locais_nao'] += isset($formulario->aci['valor']) && $formulario->aci['repasse'] == 'N' ? 1 : 0;
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

                $totalizador['programacoes']['locais']['social'] += (isset($formulario->programacoes['social']) ? intval($formulario->programacoes['social']) : 0);
                $totalizador['programacoes']['locais']['oracao'] += (isset($formulario->programacoes['oracao']) ? intval($formulario->programacoes['oracao']) : 0);
                $totalizador['programacoes']['locais']['evangelistico'] += (isset($formulario->programacoes['evangelistico']) ? intval($formulario->programacoes['evangelistico']) : 0);
                $totalizador['programacoes']['locais']['espiritual'] += (isset($formulario->programacoes['espiritual']) ? intval($formulario->programacoes['espiritual']) : 0);
                $totalizador['programacoes']['locais']['recreativo'] += (isset($formulario->programacoes['recreativo']) ? intval($formulario->programacoes['recreativo']) : 0);
            }

            $formularios_federacoes = FormularioFederacao::where('ano_referencia', $ano_referencia)
                ->when(!is_null($regiao_id), function ($sql) use ($regiao_id) {
                    return $sql->whereHas('federacao', function ($q) use ($regiao_id) {
                        return $q->where('regiao_id', $regiao_id);
                    });
                })
                ->get();

            foreach ($formularios_federacoes as $formulario) {
                $totalizador['aci']['federacoes'] += isset($formulario->aci['repasse']) && $formulario->aci['repasse'] == 'S' ? 1 : 0;
                $totalizador['aci']['federacoes_nao'] += isset($formulario->aci['repasse']) && $formulario->aci['repasse'] == 'N' ? 1 : 0;
                $totalizador['programacoes']['federacoes']['social'] += (isset($formulario->programacoes['social']) ? intval($formulario->programacoes['social']) : 0);
                $totalizador['programacoes']['federacoes']['oracao'] += (isset($formulario->programacoes['oracao']) ? intval($formulario->programacoes['oracao']) : 0);
                $totalizador['programacoes']['federacoes']['evangelistico'] += (isset($formulario->programacoes['evangelistico']) ? intval($formulario->programacoes['evangelistico']) : 0);
                $totalizador['programacoes']['federacoes']['espiritual'] += (isset($formulario->programacoes['espiritual']) ? intval($formulario->programacoes['espiritual']) : 0);
                $totalizador['programacoes']['federacoes']['recreativo'] += (isset($formulario->programacoes['recreativo']) ? intval($formulario->programacoes['recreativo']) : 0);
            }


            $formularios_sinodais = FormularioSinodal::where('ano_referencia', $ano_referencia)
                ->when(!is_null($regiao_id), function ($sql) use ($regiao_id) {
                    return $sql->whereHas('sinodal', function ($q) use ($regiao_id) {
                        return $q->where('regiao_id', $regiao_id);
                    });
                })
                ->get();

            foreach ($formularios_sinodais as $formulario) {
                $totalizador['aci']['sinodais'] += isset($formulario->aci['repasse']) && $formulario->aci['repasse'] == 'S' ? 1 : 0;
                $totalizador['aci']['sinodais_nao'] += isset($formulario->aci['repasse']) && $formulario->aci['repasse'] == 'N' ? 1 : 0;
                $totalizador['programacoes']['sinodais']['social'] += (isset($formulario->programacoes['social']) ? intval($formulario->programacoes['social']) : 0);
                $totalizador['programacoes']['sinodais']['oracao'] += (isset($formulario->programacoes['oracao']) ? intval($formulario->programacoes['oracao']) : 0);
                $totalizador['programacoes']['sinodais']['evangelistico'] += (isset($formulario->programacoes['evangelistico']) ? intval($formulario->programacoes['evangelistico']) : 0);
                $totalizador['programacoes']['sinodais']['espiritual'] += (isset($formulario->programacoes['espiritual']) ? intval($formulario->programacoes['espiritual']) : 0);
                $totalizador['programacoes']['sinodais']['recreativo'] += (isset($formulario->programacoes['recreativo']) ? intval($formulario->programacoes['recreativo']) : 0);
            }

            $totalizador['estrutura']['umps_organizadas'] = Local::where('status', true)
                ->when(!is_null($regiao_id), function ($sql) use ($regiao_id) {
                    return $sql->where('regiao_id', $regiao_id);
                })
                ->count();
            $totalizador['estrutura']['federacoes_organizadas'] = Federacao::where('status', true)
                ->when(!is_null($regiao_id), function ($sql) use ($regiao_id) {
                    return $sql->where('regiao_id', $regiao_id);
                })
                ->count();
            $totalizador['estrutura']['sinodais_organizadas'] = Sinodal::where('status', true)
                ->when(!is_null($regiao_id), function ($sql) use ($regiao_id) {
                    return $sql->where('regiao_id', $regiao_id);
                })
                ->count();

            $totalizador['abrangencia']['sinodais'] = [
                'respondido' => $formularios_sinodais->count(),
                'total' => $totalizador['estrutura']['sinodais_organizadas']
            ];

            $totalizador['abrangencia']['federacoes'] = [
                'respondido' => $formularios_federacoes->count(),
                'total' => $totalizador['estrutura']['federacoes_organizadas']
            ];

            $totalizador['abrangencia']['locais'] = [
                'respondido' => $formularios_locais->count(),
                'total' => $totalizador['estrutura']['umps_organizadas']
            ];

            $totalizador['qualidade'] = ($formularios_locais->count() * 100) / $totalizador['estrutura']['umps_organizadas'];

            return $totalizador;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
