<?php

namespace App\Services\Estatistica;

use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Regiao;
use App\Models\Sinodal;
use Illuminate\Support\Collection;

class PlanejamentoEstrategicoService
{
    private ?int $regiaoId = null;

    /** @var array<string, Collection<int, FormularioLocal>> */
    private array $cacheFormulariosLocais = [];

    /** @var array<int, array<string, true>> */
    private array $cacheSinodaisCe = [];

    public function painel(?int $ano = null, ?int $regiaoId = null): array
    {
        $this->regiaoId = $regiaoId && $regiaoId > 0 ? $regiaoId : null;
        $this->cacheFormulariosLocais = [];
        $this->cacheSinodaisCe = [];

        $config = config('planejamento_estrategico');
        $anos = range((int) $config['ano_inicio'], (int) $config['ano_fim']);
        $anoSelecionado = $ano && in_array($ano, $anos, true)
            ? $ano
            : $this->anoPadrao($anos);

        $seriePorAno = [];
        foreach ($anos as $anoCiclo) {
            $seriePorAno[$anoCiclo] = $this->calcularAno($anoCiclo, $config);
        }

        $objetivos = $this->montarObjetivos($config, $seriePorAno, $anoSelecionado);
        $participacoes = $this->montarParticipacoes($config, $seriePorAno, $anoSelecionado);
        $overview = $this->montarOverview($objetivos, $participacoes, $anoSelecionado, $config);
        $regiaoNome = $this->regiaoId
            ? Regiao::query()->where('id', $this->regiaoId)->value('nome')
            : null;

        return [
            'ciclo' => $config['ciclo'],
            'titulo' => $config['titulo'],
            'subtitulo' => $config['subtitulo'],
            'missao' => $config['missao'],
            'visao' => $config['visao'],
            'fases' => $config['fases'],
            'anos' => $anos,
            'ano_selecionado' => $anoSelecionado,
            'regiao_id' => $this->regiaoId,
            'regiao_nome' => $regiaoNome,
            'escopo_label' => $regiaoNome ? 'Região ' . $regiaoNome : 'Brasil',
            'fase_atual' => $config['fases'][$anoSelecionado] ?? null,
            'overview' => $overview,
            'objetivos' => $objetivos,
            'participacoes' => $participacoes,
            'tabela' => $this->montarTabela($objetivos, $anos),
        ];
    }

    private function anoPadrao(array $anos): int
    {
        $anoReferencia = (int) EstatisticaService::getAnoReferencia();
        if (in_array($anoReferencia, $anos, true)) {
            return $anoReferencia;
        }

        return min(max($anoReferencia, min($anos)), max($anos));
    }

    private function calcularAno(int $ano, array $config): array
    {
        $indicadores = [];

        foreach ($config['objetivos'] as $objetivo) {
            foreach ($objetivo['indicadores'] as $indicador) {
                $indicadores[$indicador['chave']] = $this->calcularIndicador($indicador, $ano);
            }
        }

        foreach ($config['participacoes'] as $participacao) {
            $indicadores[$participacao['chave']] = $this->calcularIndicador($participacao, $ano);
        }

        return $indicadores;
    }

    private function calcularIndicador(array $indicador, int $ano): array
    {
        $valor = match ($indicador['tipo']) {
            'percentual_socios_campo' => $this->percentualSociosCampo($ano, $indicador['parametros']),
            'percentual_socios_campos_soma' => $this->percentualSociosCamposSoma($ano, $indicador['parametros']),
            'percentual_umps_com_acao' => $this->percentualUmpsComAcao($ano, $indicador['parametros']),
            'percentual_sinodais_discipulado' => $this->percentualSinodaisDiscipulado($ano, $indicador['parametros']),
            'percentual_instancias_gtsi' => $this->percentualInstanciasGtsi($ano, $indicador['parametros']),
            'percentual_sinodais_ce' => $this->percentualSinodaisCe($ano),
            default => ['valor' => 0.0, 'numerador' => 0, 'denominador' => 0, 'detalhe' => 'Tipo não suportado'],
        };

        $meta = (float) ($indicador['metas_anuais'][$ano] ?? end($indicador['metas_anuais']));
        $baseline = (float) ($indicador['baseline'] ?? 0);
        $progressoMetaFinal = $this->progressoEmDirecao(
            (float) $valor['valor'],
            $baseline,
            (float) ($indicador['metas_anuais'][config('planejamento_estrategico.ano_fim')] ?? $meta)
        );
        $progressoMetaAno = $meta > 0 ? min(100, round(((float) $valor['valor'] / $meta) * 100, 1)) : 0.0;

        return array_merge($valor, [
            'meta' => $meta,
            'baseline' => $baseline,
            'progresso_meta_ano' => $progressoMetaAno,
            'progresso_meta_final' => $progressoMetaFinal,
            'status' => $this->resolverStatus($progressoMetaAno),
        ]);
    }

    private function percentualSociosCampo(int $ano, array $parametros): array
    {
        $formularios = $this->formulariosLocais($ano);
        $totalSocios = $this->totalSocios($formularios);
        $campo = $parametros['campo'] ?? '';
        $soma = 0;

        foreach ($formularios as $formulario) {
            $soma += (int) data_get($formulario, $campo, 0);
        }

        return [
            'valor' => $totalSocios > 0 ? round(($soma * 100) / $totalSocios, 1) : 0.0,
            'numerador' => $soma,
            'denominador' => $totalSocios,
            'detalhe' => number_format($soma, 0, ',', '.') . ' de ' . number_format($totalSocios, 0, ',', '.') . ' sócios',
        ];
    }

    private function percentualSociosCamposSoma(int $ano, array $parametros): array
    {
        $formularios = $this->formulariosLocais($ano);
        $totalSocios = $this->totalSocios($formularios);
        $soma = 0;

        foreach ($formularios as $formulario) {
            foreach ($parametros['campos'] ?? [] as $campo) {
                $soma += (int) data_get($formulario, $campo, 0);
            }
        }

        return [
            'valor' => $totalSocios > 0 ? round(($soma * 100) / $totalSocios, 1) : 0.0,
            'numerador' => $soma,
            'denominador' => $totalSocios,
            'detalhe' => number_format($soma, 0, ',', '.') . ' de ' . number_format($totalSocios, 0, ',', '.') . ' sócios',
        ];
    }

    private function percentualUmpsComAcao(int $ano, array $parametros): array
    {
        $total = $this->queryLocais()->count();
        if ($total === 0) {
            return ['valor' => 0.0, 'numerador' => 0, 'denominador' => 0, 'detalhe' => '0 de 0 UMPs'];
        }

        $campos = $parametros['campos_programacao'] ?? ['evangelistico', 'social'];
        $comAcao = $this->formulariosLocais($ano)
            ->filter(function (FormularioLocal $form) use ($campos) {
                $soma = 0;
                foreach ($campos as $campo) {
                    $soma += (int) data_get($form->programacoes, $campo, 0);
                }

                return $soma > 0;
            })
            ->count();

        return [
            'valor' => round(($comAcao * 100) / $total, 1),
            'numerador' => $comAcao,
            'denominador' => $total,
            'detalhe' => $comAcao . ' de ' . $total . ' UMPs',
        ];
    }

    private function percentualSinodaisDiscipulado(int $ano, array $parametros): array
    {
        $limiar = (float) ($parametros['limiar_percentual_sendo_discipulados'] ?? 20);
        $campo = $parametros['campo_discipulado'] ?? 'sendo_discipulados';
        $minimoUmps = (int) ($parametros['minimo_umps_reportando'] ?? 1);

        $sinodais = $this->querySinodais()->get(['id', 'nome']);
        $total = $sinodais->count();
        if ($total === 0) {
            return ['valor' => 0.0, 'numerador' => 0, 'denominador' => 0, 'detalhe' => '0 de 0 sinodais'];
        }

        $formularios = $this->formulariosLocais($ano);
        $porSinodal = $formularios->groupBy(fn (FormularioLocal $f) => $f->local?->sinodal_id);
        $ativas = 0;

        foreach ($sinodais as $sinodal) {
            $grupo = $porSinodal->get($sinodal->id, collect());
            if ($grupo->count() < $minimoUmps) {
                continue;
            }

            $socios = $this->totalSocios($grupo);
            if ($socios <= 0) {
                continue;
            }

            $sendo = 0;
            foreach ($grupo as $formulario) {
                $sendo += (int) data_get($formulario->discipulado, $campo, 0);
            }

            $percentual = ($sendo * 100) / $socios;
            if ($percentual >= $limiar) {
                $ativas++;
            }
        }

        return [
            'valor' => round(($ativas * 100) / $total, 1),
            'numerador' => $ativas,
            'denominador' => $total,
            'detalhe' => $ativas . ' de ' . $total . ' sinodais (≥' . $limiar . '% sendo discipulados)',
        ];
    }

    private function percentualInstanciasGtsi(int $ano, array $parametros): array
    {
        $nivel = $parametros['nivel'] ?? 'sinodal';
        $pilares = $parametros['pilares'] ?? ['estatistica', 'aci'];

        return match ($nivel) {
            'federacao' => $this->gtsiFederacoes($ano, $pilares),
            'local' => $this->gtsiLocais($ano, $pilares),
            default => $this->gtsiSinodais($ano, $pilares),
        };
    }

    private function gtsiSinodais(int $ano, array $pilares): array
    {
        $sinodais = $this->querySinodais()->get(['id']);
        $total = $sinodais->count();
        if ($total === 0) {
            return ['valor' => 0.0, 'numerador' => 0, 'denominador' => 0, 'detalhe' => '0 de 0 sinodais'];
        }

        $ids = $sinodais->pluck('id');
        $formularios = FormularioSinodal::query()
            ->where('ano_referencia', $ano)
            ->whereIn('sinodal_id', $ids)
            ->get()
            ->keyBy('sinodal_id');

        $cePorSinodal = $this->sinodaisComCe($ano);
        $ok = 0;

        foreach ($sinodais as $sinodal) {
            $form = $formularios->get($sinodal->id);
            $checks = [];

            if (in_array('estatistica', $pilares, true)) {
                $checks[] = $form !== null;
            }
            if (in_array('aci', $pilares, true)) {
                $checks[] = data_get($form?->aci, 'repasse') === 'S';
            }
            if (in_array('comissao_executiva', $pilares, true)) {
                $checks[] = isset($cePorSinodal[(string) $sinodal->id]);
            }

            if ($checks !== [] && ! in_array(false, $checks, true)) {
                $ok++;
            }
        }

        return [
            'valor' => round(($ok * 100) / $total, 1),
            'numerador' => $ok,
            'denominador' => $total,
            'detalhe' => $ok . ' de ' . $total . ' sinodais',
        ];
    }

    private function gtsiFederacoes(int $ano, array $pilares): array
    {
        $federacoes = $this->queryFederacoes()->get(['id']);
        $total = $federacoes->count();
        if ($total === 0) {
            return ['valor' => 0.0, 'numerador' => 0, 'denominador' => 0, 'detalhe' => '0 de 0 federações'];
        }

        $ids = $federacoes->pluck('id');
        $formularios = FormularioFederacao::query()
            ->where('ano_referencia', $ano)
            ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
            ->whereIn('federacao_id', $ids)
            ->get()
            ->keyBy('federacao_id');

        $ok = 0;
        foreach ($federacoes as $federacao) {
            $form = $formularios->get($federacao->id);
            $checks = [];

            if (in_array('estatistica', $pilares, true)) {
                $checks[] = $form !== null;
            }
            if (in_array('aci', $pilares, true)) {
                $checks[] = data_get($form?->aci, 'repasse') === 'S';
            }

            if ($checks !== [] && ! in_array(false, $checks, true)) {
                $ok++;
            }
        }

        return [
            'valor' => round(($ok * 100) / $total, 1),
            'numerador' => $ok,
            'denominador' => $total,
            'detalhe' => $ok . ' de ' . $total . ' federações',
        ];
    }

    private function gtsiLocais(int $ano, array $pilares): array
    {
        $total = $this->queryLocais()->count();
        if ($total === 0) {
            return ['valor' => 0.0, 'numerador' => 0, 'denominador' => 0, 'detalhe' => '0 de 0 UMPs'];
        }

        $formularios = $this->formulariosLocais($ano);
        $ok = 0;
        foreach ($formularios as $form) {
            $checks = [];

            if (in_array('estatistica', $pilares, true)) {
                $checks[] = true;
            }
            if (in_array('aci', $pilares, true)) {
                $checks[] = data_get($form->aci, 'repasse') === 'S';
            }

            if ($checks !== [] && ! in_array(false, $checks, true)) {
                $ok++;
            }
        }

        return [
            'valor' => round(($ok * 100) / $total, 1),
            'numerador' => $ok,
            'denominador' => $total,
            'detalhe' => $ok . ' de ' . $total . ' UMPs',
        ];
    }

    private function percentualSinodaisCe(int $ano): array
    {
        $sinodais = $this->querySinodais()->get(['id']);
        $total = $sinodais->count();
        if ($total === 0) {
            return ['valor' => 0.0, 'numerador' => 0, 'denominador' => 0, 'detalhe' => '0 de 0 sinodais'];
        }

        $cePorSinodal = $this->sinodaisComCe($ano);
        $presentes = $sinodais
            ->filter(fn (Sinodal $sinodal) => isset($cePorSinodal[(string) $sinodal->id]))
            ->count();

        return [
            'valor' => round(($presentes * 100) / $total, 1),
            'numerador' => $presentes,
            'denominador' => $total,
            'detalhe' => $presentes . ' de ' . $total . ' sinodais presentes na CE',
        ];
    }

    /** @return array<string, true> */
    private function sinodaisComCe(int $ano): array
    {
        if (! isset($this->cacheSinodaisCe[$ano])) {
            $query = DelegadoComissaoExecutiva::query()
                ->where('status', DelegadoComissaoExecutiva::STATUS_PRESENTE)
                ->whereNotNull('sinodal_id')
                ->whereHas('reuniao', fn ($q) => $q->where('ano', $ano));

            if ($this->regiaoId) {
                $query->whereHas('sinodal', fn ($q) => $q->where('regiao_id', $this->regiaoId)->where('status', true));
            }

            $this->cacheSinodaisCe[$ano] = $query
                ->pluck('sinodal_id')
                ->unique()
                ->mapWithKeys(fn ($id) => [(string) $id => true])
                ->all();
        }

        return $this->cacheSinodaisCe[$ano];
    }

    /** @return Collection<int, FormularioLocal> */
    private function formulariosLocais(int $ano): Collection
    {
        $cacheKey = $ano . ':' . ($this->regiaoId ?? 'all');
        if (! isset($this->cacheFormulariosLocais[$cacheKey])) {
            $this->cacheFormulariosLocais[$cacheKey] = FormularioLocal::query()
                ->where('ano_referencia', $ano)
                ->whereHas('local', function ($q) {
                    $q->where('status', true);
                    if ($this->regiaoId) {
                        $q->where('regiao_id', $this->regiaoId);
                    }
                })
                ->with('local:id,sinodal_id,regiao_id')
                ->get(['id', 'local_id', 'perfil', 'discipulado', 'programacoes', 'aci']);
        }

        return $this->cacheFormulariosLocais[$cacheKey];
    }

    private function querySinodais()
    {
        return Sinodal::query()
            ->where('status', true)
            ->when($this->regiaoId, fn ($q) => $q->where('regiao_id', $this->regiaoId));
    }

    private function queryFederacoes()
    {
        return Federacao::query()
            ->where('status', true)
            ->when($this->regiaoId, fn ($q) => $q->where('regiao_id', $this->regiaoId));
    }

    private function queryLocais()
    {
        return Local::query()
            ->where('status', true)
            ->when($this->regiaoId, fn ($q) => $q->where('regiao_id', $this->regiaoId));
    }

    /** @param  Collection<int, FormularioLocal>  $formularios */
    private function totalSocios(Collection $formularios): int
    {
        $total = 0;
        foreach ($formularios as $formulario) {
            $total += (int) data_get($formulario->perfil, 'ativos', 0)
                + (int) data_get($formulario->perfil, 'cooperadores', 0);
        }

        return $total;
    }

    private function progressoEmDirecao(float $atual, float $baseline, float $metaFinal): float
    {
        $denominador = $metaFinal - $baseline;
        if ($denominador <= 0) {
            return $atual >= $metaFinal ? 100.0 : 0.0;
        }

        return round(min(100, max(0, (($atual - $baseline) / $denominador) * 100)), 1);
    }

    private function resolverStatus(float $progressoMetaAno): array
    {
        $regras = config('planejamento_estrategico.status', []);

        foreach (['no_prazo', 'em_progresso', 'atencao', 'risco'] as $chave) {
            $regra = $regras[$chave] ?? null;
            if (! $regra) {
                continue;
            }
            if ($progressoMetaAno >= (float) $regra['min']) {
                return [
                    'chave' => $chave,
                    'label' => $regra['label'],
                    'classe' => $regra['classe'],
                ];
            }
        }

        return ['chave' => 'risco', 'label' => 'Risco', 'classe' => 'off-track'];
    }

    private function montarObjetivos(array $config, array $seriePorAno, int $anoSelecionado): array
    {
        $saida = [];

        foreach ($config['objetivos'] as $objetivo) {
            $indicadores = [];
            $principal = null;

            foreach ($objetivo['indicadores'] as $indicador) {
                $serie = [];
                foreach ($seriePorAno as $ano => $valores) {
                    $calc = $valores[$indicador['chave']] ?? null;
                    $serie[$ano] = [
                        'realizado' => $calc['valor'] ?? 0,
                        'meta' => $indicador['metas_anuais'][$ano] ?? null,
                        'detalhe' => $calc['detalhe'] ?? null,
                    ];
                }

                $atual = $seriePorAno[$anoSelecionado][$indicador['chave']] ?? null;
                $item = [
                    'chave' => $indicador['chave'],
                    'label' => $indicador['label'],
                    'descricao' => $indicador['descricao'] ?? '',
                    'unidade' => $indicador['unidade'] ?? '%',
                    'principal' => (bool) ($indicador['principal'] ?? false),
                    'baseline' => $indicador['baseline'] ?? 0,
                    'metas_anuais' => $indicador['metas_anuais'],
                    'meta_2030' => $indicador['metas_anuais'][$config['ano_fim']] ?? null,
                    'atual' => $atual,
                    'serie' => $serie,
                ];

                $indicadores[] = $item;
                if ($item['principal'] && $principal === null) {
                    $principal = $item;
                }
            }

            $saida[] = [
                'id' => $objetivo['id'],
                'titulo' => $objetivo['titulo'],
                'meta_cnm' => $objetivo['meta_cnm'],
                'cor' => $objetivo['cor'] ?? 'primary',
                'indicador_principal' => $principal,
                'indicadores' => $indicadores,
            ];
        }

        return $saida;
    }

    private function montarParticipacoes(array $config, array $seriePorAno, int $anoSelecionado): array
    {
        $saida = [];

        foreach ($config['participacoes'] as $participacao) {
            $serie = [];
            foreach ($seriePorAno as $ano => $valores) {
                $calc = $valores[$participacao['chave']] ?? null;
                $serie[$ano] = [
                    'realizado' => $calc['valor'] ?? 0,
                    'meta' => $participacao['metas_anuais'][$ano] ?? null,
                    'detalhe' => $calc['detalhe'] ?? null,
                ];
            }

            $saida[] = [
                'chave' => $participacao['chave'],
                'label' => $participacao['label'],
                'descricao' => $participacao['descricao'] ?? '',
                'unidade' => $participacao['unidade'] ?? '%',
                'metas_anuais' => $participacao['metas_anuais'],
                'meta_2030' => $participacao['metas_anuais'][$config['ano_fim']] ?? null,
                'atual' => $seriePorAno[$anoSelecionado][$participacao['chave']] ?? null,
                'serie' => $serie,
            ];
        }

        return $saida;
    }

    private function montarOverview(array $objetivos, array $participacoes, int $anoSelecionado, array $config): array
    {
        $cards = [];

        foreach ($objetivos as $objetivo) {
            $principal = $objetivo['indicador_principal'];
            if (! $principal) {
                continue;
            }
            $atual = $principal['atual'] ?? null;
            $cards[] = [
                'tipo' => 'objetivo',
                'id' => $objetivo['id'],
                'titulo' => 'Objetivo ' . $objetivo['id'],
                'valor' => $atual['valor'] ?? 0,
                'meta' => $atual['meta'] ?? ($principal['metas_anuais'][$anoSelecionado] ?? null),
                'meta_2030' => $principal['meta_2030'],
                'unidade' => $principal['unidade'],
                'status' => $atual['status'] ?? null,
                'detalhe' => $atual['detalhe'] ?? null,
            ];
        }

        foreach ($participacoes as $participacao) {
            $atual = $participacao['atual'] ?? null;
            $cards[] = [
                'tipo' => 'participacao',
                'id' => $participacao['chave'],
                'titulo' => $participacao['label'],
                'valor' => $atual['valor'] ?? 0,
                'meta' => $atual['meta'] ?? ($participacao['metas_anuais'][$anoSelecionado] ?? null),
                'meta_2030' => $participacao['meta_2030'],
                'unidade' => $participacao['unidade'],
                'status' => $atual['status'] ?? null,
                'detalhe' => $atual['detalhe'] ?? null,
            ];
        }

        $noPrazo = collect($objetivos)
            ->flatMap(fn ($o) => $o['indicadores'])
            ->filter(fn ($i) => ($i['atual']['status']['chave'] ?? '') === 'no_prazo'
                || ($i['atual']['status']['chave'] ?? '') === 'em_progresso')
            ->count();

        $totalIndicadores = collect($objetivos)->sum(fn ($o) => count($o['indicadores']));

        return [
            'ano' => $anoSelecionado,
            'fase' => $config['fases'][$anoSelecionado] ?? null,
            'cards' => $cards,
            'indicadores_no_caminho' => $noPrazo,
            'total_indicadores' => $totalIndicadores,
        ];
    }

    private function montarTabela(array $objetivos, array $anos): array
    {
        $linhas = [];

        foreach ($objetivos as $objetivo) {
            foreach ($objetivo['indicadores'] as $indicador) {
                $valores = [];
                foreach ($anos as $ano) {
                    $valores[$ano] = [
                        'realizado' => $indicador['serie'][$ano]['realizado'] ?? null,
                        'meta' => $indicador['serie'][$ano]['meta'] ?? null,
                    ];
                }

                $linhas[] = [
                    'objetivo_id' => $objetivo['id'],
                    'objetivo' => 'Objetivo ' . $objetivo['id'],
                    'indicador' => $indicador['label'],
                    'principal' => $indicador['principal'],
                    'valores' => $valores,
                ];
            }
        }

        return $linhas;
    }
}
