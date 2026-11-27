<?php

namespace App\Services\Gamificacao;

use App\Models\Gamificacao\Conquista;
use App\Models\Gamificacao\Placar;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\Enums\Liga;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\Gamificacao\Regras\DescontoResolver;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GamificacaoAcompanhamentoService
{
    public function __construct(
        private readonly CalendarioGamificacao $calendario,
        private readonly DescontoResolver $descontos,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function painel(?string $tipo = null, int|string|null $periodo = null): array
    {
        $natureza = $this->resolverNatureza($tipo);
        $ciclo = $this->calendario->ciclo();
        $exercicios = $this->exerciciosDoCiclo();
        $hoje = Carbon::now()->startOfDay();
        $anoReferencia = (int) EstatisticaService::getAnoReferencia();
        $anoAtual = $this->anoAtualNoCiclo($exercicios, $anoReferencia);
        $statusExercicios = $this->statusExercicios($exercicios, $hoje);

        $periodoNormalizado = $this->normalizarPeriodo($periodo, $anoAtual, $exercicios);
        $emCiclo = $periodoNormalizado === 'ciclo';
        $anoPeriodo = $emCiclo ? $anoAtual : (int) $periodoNormalizado;

        $placares = $this->carregarPlacares($natureza, $ciclo);
        $porInstancia = $this->agruparPorInstancia($natureza, $placares);
        $criterios = $this->criteriosDaNatureza($natureza, $anoPeriodo);
        $ligas = $this->ligasDaNatureza($natureza);
        $tetoAno = $natureza->tetoAnual($anoPeriodo);
        $tetoCiclo = $natureza->tetoCiclo();
        $faixasDesconto = GamificacaoConfiguracaoService::get("descontos.{$natureza->value}", []);

        $organizacoes = $this->montarOrganizacoes(
            $natureza,
            $porInstancia,
            $exercicios,
            $statusExercicios,
            $anoPeriodo,
            $emCiclo,
            $ciclo
        );

        $prazos = $this->prazosDoCiclo($natureza, $exercicios, $hoje);
        $adesao = $emCiclo || ($statusExercicios[$anoPeriodo] ?? '') === 'futuro'
            ? []
            : $this->montarAdesao($organizacoes, $criterios);

        return [
            'titulo' => 'Game UMP',
            'subtitulo' => 'Acompanhamento e pontuação do Planejamento Estratégico',
            'ciclo' => $ciclo,
            'hoje' => $hoje->toDateString(),
            'tipo' => $natureza->value,
            'periodo' => $periodoNormalizado,
            'exercicios' => $exercicios,
            'status_exercicios' => $statusExercicios,
            'ano_atual' => $anoAtual,
            'teto_ano' => $tetoAno,
            'teto_ciclo' => $tetoCiclo,
            'faixas_desconto' => $faixasDesconto,
            'ligas' => $ligas,
            'criterios' => $criterios,
            'situacao' => $this->montarSituacao(
                $natureza,
                $organizacoes,
                $periodoNormalizado,
                $statusExercicios,
                $prazos,
                $hoje,
                $tetoAno,
                $tetoCiclo,
                $faixasDesconto
            ),
            'prazos' => $prazos,
            'adesao' => $adesao,
            'organizacoes' => $organizacoes,
        ];
    }

    private function resolverNatureza(?string $tipo): NaturezaInstancia
    {
        return match ($tipo) {
            NaturezaInstancia::Federacao->value => NaturezaInstancia::Federacao,
            default => NaturezaInstancia::Sinodal,
        };
    }

    /**
     * @return list<int>
     */
    private function exerciciosDoCiclo(): array
    {
        $inicio = GamificacaoConfiguracaoService::getInt('ciclo_inicio', 2026);
        $fim = GamificacaoConfiguracaoService::getInt('ciclo_fim', 2029);

        return range($inicio, $fim);
    }

    /**
     * @param  list<int>  $exercicios
     */
    private function anoAtualNoCiclo(array $exercicios, int $anoReferencia): int
    {
        if (in_array($anoReferencia, $exercicios, true)) {
            return $anoReferencia;
        }

        $ano = (int) date('Y');
        if (in_array($ano, $exercicios, true)) {
            return $ano;
        }

        return $exercicios[0];
    }

    /**
     * @param  list<int>  $exercicios
     * @return array<int, string>
     */
    private function statusExercicios(array $exercicios, Carbon $hoje): array
    {
        $status = [];
        foreach ($exercicios as $ano) {
            $inicio = Carbon::create($ano, 1, 1)->startOfDay();
            if ($hoje->lt($inicio)) {
                $status[$ano] = 'futuro';
            } elseif ($this->calendario->anoFechado($ano, $hoje)) {
                $status[$ano] = 'definitivo';
            } else {
                $status[$ano] = 'andamento';
            }
        }

        return $status;
    }

    /**
     * @param  list<int>  $exercicios
     */
    private function normalizarPeriodo(int|string|null $periodo, int $anoAtual, array $exercicios): int|string
    {
        if ($periodo === 'ciclo') {
            return 'ciclo';
        }

        $ano = is_numeric($periodo) ? (int) $periodo : $anoAtual;

        return in_array($ano, $exercicios, true) ? $ano : $anoAtual;
    }

    private function carregarPlacares(NaturezaInstancia $natureza, string $ciclo): Collection
    {
        $with = ['pilares'];
        if ($natureza === NaturezaInstancia::Sinodal) {
            $with[] = 'sinodal';
        } else {
            $with[] = 'federacao.sinodal';
        }

        return Placar::withoutGlobalScopes()
            ->with($with)
            ->where('ciclo', $ciclo)
            ->when(
                $natureza === NaturezaInstancia::Sinodal,
                fn ($q) => $q->whereNotNull('sinodal_id'),
                fn ($q) => $q->whereNotNull('federacao_id')
            )
            ->orderBy('ano_referencia')
            ->get();
    }

    /**
     * @return Collection<string, Collection<int, Placar>>
     */
    private function agruparPorInstancia(NaturezaInstancia $natureza, Collection $placares): Collection
    {
        return $placares->groupBy(
            fn (Placar $placar) => $natureza === NaturezaInstancia::Sinodal
                ? (string) $placar->sinodal_id
                : (string) $placar->federacao_id
        );
    }

    /**
     * @return list<array{id: string, label: string, faixa: string}>
     */
    private function ligasDaNatureza(NaturezaInstancia $natureza): array
    {
        return collect(Liga::cases())->map(fn (Liga $liga) => [
            'id' => $liga->value,
            'label' => $liga->label(),
            'faixa' => (string) GamificacaoConfiguracaoService::get(
                "ligas.{$natureza->value}.{$liga->value}.faixa",
                ''
            ),
        ])->values()->all();
    }

    /**
     * @return list<array{id: string, label: string, curto: string, max: int}>
     */
    private function criteriosDaNatureza(NaturezaInstancia $natureza, int $ano): array
    {
        return collect($this->pilaresAplicaveis($natureza, $ano))
            ->map(fn (Pilar $pilar) => [
                'id' => $pilar->value,
                'label' => $pilar->label(),
                'curto' => $this->rotuloCurto($pilar),
                'max' => $pilar->maximo(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<Pilar>
     */
    private function pilaresAplicaveis(NaturezaInstancia $natureza, int $ano): array
    {
        $inicio = GamificacaoConfiguracaoService::getInt('ciclo_inicio', 2026);
        $pilares = [Pilar::Estatistica, Pilar::Aci];

        if ($ano <= $inicio) {
            return $pilares;
        }

        $pilares[] = Pilar::Evangelismo;

        if ($natureza === NaturezaInstancia::Sinodal) {
            $pilares[] = Pilar::ComissaoExecutiva;
            $pilares[] = Pilar::ConexaoMissionaria;
        } else {
            $pilares[] = Pilar::EntregaAntecipada;
            $pilares[] = Pilar::Eventos;
        }

        return $pilares;
    }

    private function rotuloCurto(Pilar $pilar): string
    {
        return match ($pilar) {
            Pilar::Estatistica => 'Estatística',
            Pilar::Aci => 'ACI',
            Pilar::Evangelismo => 'Evang./Social',
            Pilar::ComissaoExecutiva => 'Com. Exec.',
            Pilar::ConexaoMissionaria => 'Conexão',
            Pilar::EntregaAntecipada => 'Bônus',
            Pilar::Eventos => 'Eventos',
        };
    }

    /**
     * @param  Collection<string, Collection<int, Placar>>  $porInstancia
     * @param  list<int>  $exercicios
     * @param  array<int, string>  $statusExercicios
     * @return list<array<string, mixed>>
     */
    private function montarOrganizacoes(
        NaturezaInstancia $natureza,
        Collection $porInstancia,
        array $exercicios,
        array $statusExercicios,
        int $anoPeriodo,
        bool $emCiclo,
        string $ciclo
    ): array {
        $selosPorInstancia = $this->carregarSelos($natureza, $ciclo);

        $organizacoes = $porInstancia->map(function (Collection $placaresAno, string $instanciaId) use (
            $natureza,
            $exercicios,
            $statusExercicios,
            $anoPeriodo,
            $emCiclo,
            $selosPorInstancia
        ) {
            /** @var Collection<int, Placar> $porAno */
            $porAno = $placaresAno->keyBy(fn (Placar $p) => (int) $p->ano_referencia);
            if ($emCiclo) {
                $placarRef = $porAno->sortKeysDesc()->first();
            } else {
                $placarRef = $porAno->get($anoPeriodo);
            }
            if (! $placarRef) {
                return null;
            }

            $instancia = $natureza === NaturezaInstancia::Sinodal
                ? $placarRef->sinodal
                : $placarRef->federacao;

            if (! $instancia) {
                return null;
            }

            $anos = [];
            foreach ($exercicios as $ano) {
                $placarAno = $porAno->get($ano);
                $anos[$ano] = [
                    'pontos' => $placarAno?->pontos_ano,
                    'max' => $natureza->tetoAnual($ano),
                    'status' => $statusExercicios[$ano] ?? 'futuro',
                ];
            }

            $pontosCiclo = (int) ($porAno->sortKeysDesc()->first()?->pontos_ciclo ?? 0);
            $desconto = $this->descontos->projetar($natureza, $pontosCiclo);
            $pilares = $this->mapearPilares(
                $porAno->get($anoPeriodo),
                $natureza,
                $anoPeriodo,
                ($statusExercicios[$anoPeriodo] ?? 'futuro') !== 'andamento'
            );

            $selos = $selosPorInstancia->get($instanciaId, collect())
                ->filter(fn (array $selo) => $selo['ano'] === $anoPeriodo || $emCiclo)
                ->unique('tipo')
                ->values()
                ->map(fn (array $selo) => [
                    'tipo' => $selo['tipo'],
                    'label' => $selo['label'],
                ])
                ->all();

            if (! $emCiclo && ($porAno->get($anoPeriodo)?->posicao_liga ?? 0) > 0
                && ($porAno->get($anoPeriodo)?->posicao_liga ?? 99) <= 3
                && in_array($statusExercicios[$anoPeriodo] ?? '', ['definitivo'], true)
            ) {
                array_unshift($selos, [
                    'tipo' => 'podio',
                    'label' => $porAno->get($anoPeriodo)->posicao_liga.'º da Liga',
                ]);
            }

            return [
                'id' => $instanciaId,
                'nome' => (string) ($instancia->nome ?? $instancia->sigla ?? 'Organização'),
                'sigla' => (string) ($instancia->sigla ?? ''),
                'sinodal' => $natureza === NaturezaInstancia::Federacao
                    ? (string) ($placarRef->federacao?->sinodal?->nome ?? $placarRef->federacao?->sinodal?->sigla ?? '')
                    : null,
                'liga' => $placarRef->liga?->value ?? Liga::Conceicao->value,
                'socios' => (int) $placarRef->socios_ativos,
                'posicao' => (int) ($porAno->get($anoPeriodo)?->posicao_liga
                    ?? $placarRef->posicao_liga
                    ?? 0),
                'total_na_liga' => (int) ($porAno->get($anoPeriodo)?->total_na_liga
                    ?? $placarRef->total_na_liga
                    ?? 0),
                'pontos_ano' => (int) ($porAno->get($anoPeriodo)?->pontos_ano ?? 0),
                'pontos_ciclo' => $pontosCiclo,
                'desconto_projetado' => $desconto,
                'selos' => $selos,
                'pilares' => $pilares,
                'anos' => $anos,
            ];
        })->filter()->values();

        if ($emCiclo) {
            return $organizacoes
                ->sortByDesc('pontos_ciclo')
                ->values()
                ->all();
        }

        return $organizacoes
            ->sort(function (array $a, array $b) {
                if ($a['liga'] !== $b['liga']) {
                    return $this->ordemLiga($a['liga']) <=> $this->ordemLiga($b['liga']);
                }
                if ($a['posicao'] !== $b['posicao']) {
                    return $a['posicao'] <=> $b['posicao'];
                }

                return $b['pontos_ano'] <=> $a['pontos_ano'];
            })
            ->values()
            ->all();
    }

    private function ordemLiga(string $liga): int
    {
        return match ($liga) {
            Liga::Simonton->value => 0,
            Liga::Blackford->value => 1,
            default => 2,
        };
    }

    /**
     * @return Collection<string, Collection<int, array{ano: int, tipo: string, label: string}>>
     */
    private function carregarSelos(NaturezaInstancia $natureza, string $ciclo): Collection
    {
        $campo = $natureza === NaturezaInstancia::Sinodal ? 'sinodal_id' : 'federacao_id';

        return Conquista::withoutGlobalScopes()
            ->where('ciclo', $ciclo)
            ->whereIn('tipo', [
                TipoConquista::SeloEntregaAntecipada->value,
                TipoConquista::SeloEngajamento100->value,
            ])
            ->whereNotNull($campo)
            ->get()
            ->groupBy($campo)
            ->map(fn (Collection $itens) => $itens->map(fn (Conquista $selo) => [
                'ano' => (int) $selo->ano_referencia,
                'tipo' => $selo->tipo->value,
                'label' => $selo->tipo->label(),
            ]));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function mapearPilares(
        ?Placar $placar,
        NaturezaInstancia $natureza,
        int $ano,
        bool $encerrado
    ): array {
        $aplicaveis = $this->pilaresAplicaveis($natureza, $ano);
        $porPilar = $placar?->pilares?->keyBy(fn ($p) => $p->pilar->value) ?? collect();

        return collect($aplicaveis)->map(function (Pilar $pilar) use ($porPilar, $encerrado) {
            $resultado = $porPilar->get($pilar->value);
            $pontos = (int) ($resultado?->pontos ?? 0);
            $max = (int) ($resultado?->pontos_maximo ?? $pilar->maximo());
            $detalhes = $resultado?->detalhes ?? [];
            $status = $resultado?->status;
            $medida = (string) ($detalhes['rodape'] ?? $detalhes['medida'] ?? '');
            if ($pilar === Pilar::Evangelismo && str_contains($medida, '60%=')) {
                $medida = explode(' · ', $medida)[0];
            }

            return [
                'id' => $pilar->value,
                'label' => $pilar->label(),
                'curto' => $this->rotuloCurto($pilar),
                'pontos' => $pontos,
                'max' => $max,
                'situacao' => $this->situacaoPilar($pontos, $max, $status, $encerrado),
                'medida' => $medida,
                'progresso' => $max > 0 ? min(1, $pontos / $max) : 0,
                'status' => $status?->value,
            ];
        })->values()->all();
    }

    private function situacaoPilar(
        int $pontos,
        int $max,
        ?StatusPilar $status,
        bool $encerrado
    ): string {
        if ($max > 0 && $pontos >= $max) {
            return 'atingido';
        }
        if ($pontos > 0) {
            return 'parcial';
        }
        if ($status === StatusPilar::Indisponivel) {
            return 'pendente';
        }
        if (! $encerrado) {
            return 'pendente';
        }

        return 'perdido';
    }

    /**
     * @param  list<array<string, mixed>>  $organizacoes
     * @param  list<array{id: string, label: string, curto: string, max: int}>  $criterios
     * @return list<array<string, mixed>>
     */
    private function montarAdesao(array $organizacoes, array $criterios): array
    {
        $total = count($organizacoes);
        if ($total === 0) {
            return [];
        }

        return collect($criterios)->map(function (array $criterio) use ($organizacoes, $total) {
            $itens = collect($organizacoes)->map(
                fn (array $org) => collect($org['pilares'])->firstWhere('id', $criterio['id'])
            )->filter();

            $atingido = $itens->where('situacao', 'atingido')->count();
            $parcial = $itens->where('situacao', 'parcial')->count();

            return [
                'id' => $criterio['id'],
                'nome' => $criterio['label'],
                'max' => $criterio['max'],
                'atingido' => $atingido,
                'parcial' => $parcial,
                'total' => $total,
            ];
        })->values()->all();
    }

    /**
     * @param  list<int>  $exercicios
     * @return list<array{data: string, titulo: string, nota: string}>
     */
    private function prazosDoCiclo(NaturezaInstancia $natureza, array $exercicios, Carbon $hoje): array
    {
        $lista = [];
        $inicio = GamificacaoConfiguracaoService::getInt('ciclo_inicio', 2026);

        foreach ($exercicios as $ano) {
            if ($natureza === NaturezaInstancia::Federacao && $ano > $inicio) {
                $data = $this->calendario->prazoEntregaAntecipada($ano);
                $lista[] = [
                    'data' => $data->toDateString(),
                    'titulo' => "Bônus de entrega antecipada {$ano}",
                    'nota' => '80% das UMPs com formulário',
                ];
            }

            $fechamento = $this->calendario->prazoEstatistica($ano);
            $lista[] = [
                'data' => $fechamento->toDateString(),
                'titulo' => "Fechamento do exercício {$ano}",
                'nota' => $natureza === NaturezaInstancia::Sinodal && $ano > $inicio
                    ? 'Formulários, ACI e relatório da Conexão Missionária'
                    : 'Formulários e ACI',
            ];
        }

        usort($lista, fn ($a, $b) => $a['data'] <=> $b['data']);

        return array_map(function (array $item) use ($hoje) {
            $data = Carbon::parse($item['data'])->startOfDay();
            $item['passou'] = $data->lt($hoje);
            $item['dias'] = (int) $hoje->diffInDays($data, false);

            // Carbon 3 pode devolver float absoluto; normaliza sinal pelo calendário.
            if ($item['passou'] && $item['dias'] > 0) {
                $item['dias'] = -$item['dias'];
            } elseif (! $item['passou'] && $item['dias'] < 0) {
                $item['dias'] = abs($item['dias']);
            }

            return $item;
        }, $lista);
    }

    /**
     * @param  list<array<string, mixed>>  $organizacoes
     * @param  array<int, string>  $statusExercicios
     * @param  list<array<string, mixed>>  $prazos
     * @param  array<int|string, int>  $faixasDesconto
     * @return array<string, mixed>
     */
    private function montarSituacao(
        NaturezaInstancia $natureza,
        array $organizacoes,
        int|string $periodo,
        array $statusExercicios,
        array $prazos,
        Carbon $hoje,
        int $tetoAno,
        int $tetoCiclo,
        array $faixasDesconto
    ): array {
        $proximo = collect($prazos)->first(fn (array $p) => ! $p['passou']);
        $proximoPrazo = $proximo ? [
            'data' => $proximo['data'],
            'titulo' => $proximo['titulo'],
            'dias' => max(0, (int) $proximo['dias']),
        ] : null;

        $total = count($organizacoes);
        $rotulos = [
            'andamento' => 'Em andamento',
            'definitivo' => 'Resultado definitivo',
            'futuro' => 'Não iniciado',
        ];

        if ($periodo === 'ciclo') {
            $definitivos = collect($statusExercicios)->filter(fn ($s) => $s === 'definitivo')->keys()->values()->all();
            $minimo55 = (int) ($faixasDesconto[50] ?? (int) ceil($tetoCiclo * 0.55));
            $noRitmo = collect($organizacoes)->filter(function (array $org) use ($definitivos, $natureza) {
                if ($definitivos === []) {
                    return false;
                }
                $pontos = 0;
                $max = 0;
                foreach ($definitivos as $ano) {
                    $pontos += (int) ($org['anos'][$ano]['pontos'] ?? 0);
                    $max += $natureza->tetoAnual((int) $ano);
                }

                return $max > 0 && ($pontos * 100) >= ($max * 55);
            })->count();

            return [
                'modo' => 'ciclo',
                'exercicios_definitivos' => count($definitivos),
                'exercicios_definitivos_label' => $definitivos === [] ? 'Nenhum resultado fechado' : implode(' e ', $definitivos),
                'no_ritmo' => $noRitmo,
                'total_orgs' => $total,
                'teto_ciclo' => $tetoCiclo,
                'faixa_100' => (int) ($faixasDesconto[100] ?? 0),
                'proximo_prazo' => $proximoPrazo,
            ];
        }

        $status = $statusExercicios[(int) $periodo] ?? 'futuro';
        $media = null;
        if ($status !== 'futuro' && $total > 0) {
            $media = (int) round(collect($organizacoes)->avg('pontos_ano'));
        }

        $notas = [
            'andamento' => 'Pontos parciais. Dados aceitos até '.$this->calendario->prazoEstatistica((int) $periodo)->format('d/m/Y'),
            'definitivo' => 'Resultado definitivo do exercício',
            'futuro' => 'Começa em 01/01/'.$periodo,
        ];

        return [
            'modo' => 'ano',
            'ano' => (int) $periodo,
            'status' => $status,
            'status_label' => $rotulos[$status] ?? $status,
            'nota' => $notas[$status] ?? '',
            'media' => $media,
            'maximo' => $tetoAno,
            'total_orgs' => $total,
            'proximo_prazo' => $proximoPrazo,
        ];
    }
}
