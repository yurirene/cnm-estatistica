<?php

namespace App\Services\Estatistica;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioLocal;
use App\Models\Local;
use App\Models\Regiao;
use App\Services\MapaService;

class PainelNacionalService
{
    public static function complemento(array $request, array $dados): array
    {
        $ano = (int) $request['ano'];
        $regiao = $request['regiao'];
        $totalSocios = (int) (($dados['perfil']['ativos'] ?? 0) + ($dados['perfil']['cooperadores'] ?? 0));

        $evolucao = self::evolucao($ano, $regiao);
        $barras = self::sociosBarras($ano, $regiao);
        $ranking = self::rankingFederacoes($ano, $regiao);
        $financeiro = self::financeiro($dados);

        return [
            'visao_geral' => [
                'evolucao' => $evolucao,
                'socios_barras' => $barras['itens'],
                'socios_barras_titulo' => $barras['titulo'],
                'ranking_federacoes' => $ranking,
                'insights' => self::insights($ano, $regiao, $dados, $totalSocios, $barras['itens'], $financeiro),
                'funil' => self::funil($dados),
                'medias' => self::medias($dados, $totalSocios),
            ],
            'perfil' => self::perfil($dados, $totalSocios),
            'espiritualidade' => [
                'discipulado' => self::discipulado($dados, $totalSocios),
            ],
            'inclusao' => self::inclusao($dados, $totalSocios),
            'financeiro' => $financeiro,
        ];
    }

    private static function evolucao(int $ano, ?int $regiao): array
    {
        $labels = [];
        $resposta = [];
        $aci = [];

        for ($i = 3; $i >= 0; $i--) {
            $anoSerie = $ano - $i;
            $labels[] = (string) $anoSerie;

            $totalUmps = Local::query()
                ->where('status', true)
                ->when($regiao, fn ($q) => $q->where('regiao_id', $regiao))
                ->count();

            $entregues = FormularioLocal::query()
                ->where('ano_referencia', $anoSerie)
                ->whereHas('local', function ($q) use ($regiao) {
                    $q->where('status', true);
                    if ($regiao) {
                        $q->where('regiao_id', $regiao);
                    }
                })
                ->count();

            $resposta[] = $totalUmps > 0 ? round(($entregues * 100) / $totalUmps, 1) : 0.0;

            $formularios = FormularioLocal::query()
                ->where('ano_referencia', $anoSerie)
                ->whereHas('local', function ($q) use ($regiao) {
                    $q->where('status', true);
                    if ($regiao) {
                        $q->where('regiao_id', $regiao);
                    }
                })
                ->get(['aci']);

            $sim = 0;
            $nao = 0;
            foreach ($formularios as $formulario) {
                $repasse = $formulario->aci['repasse'] ?? null;
                if ($repasse === 'S') {
                    $sim++;
                } elseif ($repasse === 'N') {
                    $nao++;
                }
            }
            $aci[] = ($sim + $nao) > 0 ? round(($sim * 100) / ($sim + $nao), 1) : 0.0;
        }

        return [
            'labels' => $labels,
            'resposta' => $resposta,
            'aci' => $aci,
        ];
    }

    private static function sociosBarras(int $ano, ?int $regiao): array
    {
        if ($regiao) {
            $estados = Estado::query()
                ->where('regiao_id', $regiao)
                ->orderBy('nome')
                ->get(['id', 'nome', 'sigla']);

            $itens = [];
            foreach ($estados as $estado) {
                $totalizador = MapaService::getTotalizador('br-' . strtolower($estado->sigla), (string) $ano);
                $itens[] = [
                    'nome' => $estado->nome,
                    'valor' => (int) ($totalizador['n_socios'] ?? 0),
                ];
            }

            return [
                'titulo' => 'Sócios por estado',
                'itens' => $itens,
            ];
        }

        $regioes = Regiao::query()->orderBy('id')->get(['id', 'nome']);
        $totais = [];
        foreach ($regioes as $item) {
            $totais[$item->id] = ['nome' => $item->nome, 'valor' => 0];
        }

        $formularios = FormularioLocal::query()
            ->select(['formularios_local_v1.perfil', 'locais.regiao_id'])
            ->join('locais', 'locais.id', '=', 'formularios_local_v1.local_id')
            ->where('formularios_local_v1.ano_referencia', $ano)
            ->where('locais.status', true)
            ->whereNull('formularios_local_v1.deleted_at')
            ->whereNull('locais.deleted_at')
            ->get();

        foreach ($formularios as $formulario) {
            $regiaoId = (int) $formulario->regiao_id;
            if (!isset($totais[$regiaoId])) {
                continue;
            }
            $totais[$regiaoId]['valor'] += intval($formulario->perfil['ativos'] ?? 0)
                + intval($formulario->perfil['cooperadores'] ?? 0);
        }

        $itens = array_values($totais);
        usort($itens, fn ($a, $b) => $b['valor'] <=> $a['valor']);

        return [
            'titulo' => 'Sócios por região',
            'itens' => $itens,
        ];
    }

    private static function rankingFederacoes(int $ano, ?int $regiao): array
    {
        $federacoes = Federacao::query()
            ->where('status', true)
            ->when($regiao, fn ($q) => $q->where('regiao_id', $regiao))
            ->get(['id', 'nome']);

        if ($federacoes->isEmpty()) {
            return [];
        }

        $ids = $federacoes->pluck('id');
        $totais = Local::query()
            ->where('status', true)
            ->whereIn('federacao_id', $ids)
            ->selectRaw('federacao_id, COUNT(*) as total')
            ->groupBy('federacao_id')
            ->pluck('total', 'federacao_id');

        $entregues = FormularioLocal::query()
            ->selectRaw('locais.federacao_id, COUNT(*) as total')
            ->join('locais', 'locais.id', '=', 'formularios_local_v1.local_id')
            ->whereIn('locais.federacao_id', $ids)
            ->where('locais.status', true)
            ->where('formularios_local_v1.ano_referencia', $ano)
            ->whereNull('formularios_local_v1.deleted_at')
            ->whereNull('locais.deleted_at')
            ->groupBy('locais.federacao_id')
            ->pluck('total', 'federacao_id');

        $lista = $federacoes
            ->map(function (Federacao $federacao) use ($totais, $entregues) {
                $total = (int) ($totais[$federacao->id] ?? 0);
                $respondido = (int) ($entregues[$federacao->id] ?? 0);
                $percentual = $total > 0 ? round(($respondido * 100) / $total, 1) : 0.0;

                return [
                    'id' => $federacao->id,
                    'nome' => $federacao->nome,
                    'percentual' => $percentual,
                    'total' => $total,
                ];
            })
            ->filter(fn (array $item) => $item['total'] > 0)
            ->sortByDesc('percentual')
            ->values();

        $total = $lista->count();
        if ($total === 0) {
            return [];
        }

        $melhores = $lista->take(3)->values()->map(function (array $item, int $index) {
            $item['pos'] = $index + 1;
            $item['tipo'] = 'good';

            return $item;
        });

        $piores = $lista->reverse()->take(3)->values()->map(function (array $item, int $index) use ($total) {
            $item['pos'] = $total - $index;
            $item['tipo'] = 'bad';

            return $item;
        })->sortBy('pos')->values();

        $idsUsados = [];
        $ranking = [];
        foreach ($melhores->concat($piores) as $item) {
            if (isset($idsUsados[$item['id']])) {
                continue;
            }
            $idsUsados[$item['id']] = true;
            unset($item['id'], $item['total']);
            $ranking[] = $item;
        }

        return $ranking;
    }

    private static function funil(array $dados): array
    {
        $niveis = [
            ['chave' => 'sinodais', 'label' => 'Sinodais'],
            ['chave' => 'federacoes', 'label' => 'Federações'],
            ['chave' => 'locais', 'label' => 'UMPs Locais'],
        ];

        $passos = [];
        foreach ($niveis as $nivel) {
            $respondido = (int) ($dados['abrangencia'][$nivel['chave']]['respondido'] ?? 0);
            $total = (int) ($dados['abrangencia'][$nivel['chave']]['total'] ?? 0);
            $percentual = $total > 0 ? round(($respondido * 100) / $total) : 0;
            $passos[] = [
                'label' => $nivel['label'],
                'percentual' => $percentual,
                'texto' => $respondido . '/' . $total,
            ];
        }

        return $passos;
    }

    private static function medias(array $dados, int $totalSocios): array
    {
        $umps = max(1, (int) ($dados['estrutura']['umps_organizadas'] ?? 0));
        $federacoes = max(1, (int) ($dados['estrutura']['federacoes_organizadas'] ?? 0));
        $sinodais = max(1, (int) ($dados['estrutura']['sinodais_organizadas'] ?? 0));
        $umpsReal = (int) ($dados['estrutura']['umps_organizadas'] ?? 0);
        $federacoesReal = (int) ($dados['estrutura']['federacoes_organizadas'] ?? 0);

        return [
            [
                'valor' => $umpsReal > 0 ? number_format($totalSocios / $umps, 1, ',', '.') : '0',
                'label' => 'Sócios por UMP',
            ],
            [
                'valor' => $federacoesReal > 0 ? number_format($umpsReal / $federacoes, 1, ',', '.') : '0',
                'label' => 'UMPs por Federação',
            ],
            [
                'valor' => $sinodais > 0 && ($dados['estrutura']['sinodais_organizadas'] ?? 0) > 0
                    ? number_format($federacoesReal / $sinodais, 1, ',', '.')
                    : '0',
                'label' => 'Federações por Sinodal',
            ],
        ];
    }

    private static function perfil(array $dados, int $totalSocios): array
    {
        $ativos = (int) ($dados['perfil']['ativos'] ?? 0);
        $cooperadores = (int) ($dados['perfil']['cooperadores'] ?? 0);

        return [
            'ativos' => $ativos,
            'cooperadores' => $cooperadores,
            'ativos_pct' => $totalSocios > 0 ? round(($ativos * 100) / $totalSocios) : 0,
            'cooperadores_pct' => $totalSocios > 0 ? round(($cooperadores * 100) / $totalSocios) : 0,
        ];
    }

    private static function discipulado(array $dados, int $totalSocios): array
    {
        $campos = [
            'sendo_discipulados' => 'Sendo discipulados',
            'discipulando_outro' => 'Outro método',
            'trilha_cnm' => 'Trilha da CNM',
            'discipulando_cnm' => 'Método da CNM',
        ];

        $passos = [
            [
                'label' => 'Total de sócios',
                'valor' => $totalSocios,
                'percentual' => 100,
            ],
        ];

        foreach ($campos as $chave => $label) {
            $valor = (int) ($dados['discipulado'][$chave] ?? 0);
            $passos[] = [
                'label' => $label,
                'valor' => $valor,
                'percentual' => $totalSocios > 0 ? round(($valor * 100) / $totalSocios) : 0,
            ];
        }

        return $passos;
    }

    private static function inclusao(array $dados, int $totalSocios): array
    {
        $grupos = [
            ['label' => 'Visual', 'campos' => ['cegos', 'baixa_visao']],
            ['label' => 'Auditiva', 'campos' => ['auditiva', 'surdos']],
            ['label' => 'Física', 'campos' => ['fisica_inferior', 'fisica_superior']],
            ['label' => 'Neuro/Intelectual', 'campos' => ['neurologico', 'intelectual']],
        ];

        $categorias = [];
        foreach ($grupos as $grupo) {
            $total = 0;
            foreach ($grupo['campos'] as $campo) {
                $total += (int) ($dados['deficiencias'][$campo] ?? 0);
            }
            $categorias[] = [
                'label' => $grupo['label'],
                'total' => $total,
                'percentual' => $totalSocios > 0 ? round(($total * 100) / $totalSocios, 1) : 0.0,
            ];
        }

        return ['categorias' => $categorias];
    }

    private static function financeiro(array $dados): array
    {
        $niveis = [
            'umps' => ['sim' => 'locais', 'nao' => 'locais_nao', 'label' => 'UMPs Locais'],
            'federacoes' => ['sim' => 'federacoes', 'nao' => 'federacoes_nao', 'label' => 'Federações'],
            'sinodais' => ['sim' => 'sinodais', 'nao' => 'sinodais_nao', 'label' => 'Sinodais'],
        ];

        $saida = [];
        foreach ($niveis as $chave => $nivel) {
            $sim = (int) ($dados['aci'][$nivel['sim']] ?? 0);
            $nao = (int) ($dados['aci'][$nivel['nao']] ?? 0);
            $base = $sim + $nao;
            $saida[$chave] = [
                'label' => $nivel['label'],
                'sim' => $sim,
                'nao' => $nao,
                'taxa' => $base > 0 ? round(($sim * 100) / $base, 1) : 0.0,
            ];
        }

        return $saida;
    }

    private static function insights(
        int $ano,
        ?int $regiao,
        array $dados,
        int $totalSocios,
        array $barras,
        array $financeiro
    ): array {
        $insights = [];

        $sociosAnterior = self::totalSociosAno($ano - 1, $regiao);
        if ($sociosAnterior > 0) {
            $variacao = round((($totalSocios - $sociosAnterior) * 100) / $sociosAnterior, 1);
            $direcao = $variacao >= 0 ? 'cresceu' : 'recuou';
            $insights[] = 'O total de sócios '
                . $direcao
                . ' <b>' . number_format(abs($variacao), 1, ',', '.') . '%</b> em relação a '
                . ($ano - 1)
                . '.';
        } elseif ($totalSocios > 0) {
            $insights[] = 'Foram reportados <b>' . number_format($totalSocios, 0, ',', '.') . '</b> sócios em ' . $ano . '.';
        }

        if (!$regiao && count($barras) > 1) {
            $lider = $barras[0];
            $totalBarras = array_sum(array_column($barras, 'valor'));
            if ($totalBarras > 0 && $lider['valor'] > 0) {
                $pct = round(($lider['valor'] * 100) / $totalBarras);
                $insights[] = 'A <b>Região ' . e($lider['nome']) . ' concentra ' . $pct . '%</b> dos sócios no recorte atual.';
            }
        }

        $umps = (int) ($dados['abrangencia']['locais']['total'] ?? 0);
        $umpsResp = (int) ($dados['abrangencia']['locais']['respondido'] ?? 0);
        $qualidade = $umps > 0 ? round(($umpsResp * 100) / $umps, 1) : 0;
        $insights[] = 'A qualidade do relatório está em <b>' . number_format($qualidade, 1, ',', '.') . '%</b> ('
            . $umpsResp . '/' . $umps . ' UMPs locais).';

        $taxaUmp = $financeiro['umps']['taxa'] ?? 0;
        $insights[] = 'O repasse de ACI entre UMPs ficou em <b>' . number_format($taxaUmp, 1, ',', '.') . '%</b> das que informaram o campo.';

        return $insights;
    }

    private static function totalSociosAno(int $ano, ?int $regiao): int
    {
        $formularios = FormularioLocal::query()
            ->where('ano_referencia', $ano)
            ->whereHas('local', function ($q) use ($regiao) {
                $q->where('status', true);
                if ($regiao) {
                    $q->where('regiao_id', $regiao);
                }
            })
            ->get(['perfil']);

        $total = 0;
        foreach ($formularios as $formulario) {
            $total += intval($formulario->perfil['ativos'] ?? 0)
                + intval($formulario->perfil['cooperadores'] ?? 0);
        }

        return $total;
    }
}
