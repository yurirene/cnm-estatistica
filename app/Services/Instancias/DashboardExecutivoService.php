<?php

namespace App\Services\Instancias;

use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\ValorAciAno;
use App\Services\ComprovanteAciService;
use App\Services\Estatistica\EstatisticaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class DashboardExecutivoService
{
    private ?Collection $idsSinodais = null;

    public function __construct(private bool $nacional = false)
    {
    }

    public static function make(): self
    {
        return new self(Gate::check(['presidente']));
    }

    public static function getHero(): array
    {
        return self::make()->hero();
    }

    public static function getQualidade(): array
    {
        return self::make()->qualidade();
    }

    public static function getRankingUrgencia(int $limite = 5): array
    {
        return self::make()->rankingUrgencia($limite);
    }

    public function hero(): array
    {
        $ano = (int) EstatisticaService::getAnoReferencia();
        $anoAnterior = $ano - 1;

        $qualidade = $this->qualidadeParaAno($ano);
        $qualidadeAnterior = $this->qualidadeParaAno($anoAnterior);

        $socios = $this->totalSocios($ano);
        $sociosAnterior = $this->totalSocios($anoAnterior);
        $variacaoSocios = $this->variacaoPercentual($socios, $sociosAnterior);

        $aciArrecadada = $this->aciArrecadada($ano);
        $aciAnterior = $this->aciArrecadada($anoAnterior);
        $aciMeta = $this->aciMeta($ano);
        $aciPercentual = $aciMeta > 0 ? min(100, round(($aciArrecadada * 100) / $aciMeta, 1)) : 0;

        $taxaAci = $aciPercentual;
        $crescimentoScore = $this->crescimentoParaScore($variacaoSocios);
        $score = (int) round(
            (0.4 * $qualidade['percentual']) + (0.4 * $taxaAci) + (0.2 * $crescimentoScore)
        );
        $score = max(0, min(100, $score));

        $alertas = $this->alertasParaAno($ano);
        $alertasAnterior = $this->alertasParaAno($anoAnterior);

        return [
            'ano' => $ano,
            'ano_anterior' => $anoAnterior,
            'saude' => [
                'score' => $score,
                'rotulo' => $this->rotuloSaude($score),
                'cor' => $this->corSaude($score),
                'dashoffset' => round(201 - ((201 * $score) / 100), 1),
            ],
            'socios' => [
                'total' => $socios,
                'total_formatado' => number_format($socios, 0, ',', '.'),
                'variacao' => $variacaoSocios,
                'variacao_formatada' => $this->formatarVariacao($variacaoSocios),
                'positiva' => $variacaoSocios >= 0,
                'serie' => $this->serieSocios($ano),
            ],
            'aci' => [
                'arrecadada' => $aciArrecadada,
                'arrecadada_formatada' => $this->formatarMoedaCompacta($aciArrecadada),
                'meta' => $aciMeta,
                'meta_formatada' => $this->formatarMoedaCompacta($aciMeta),
                'percentual' => $aciPercentual,
                'anterior' => $aciAnterior,
                'anterior_formatada' => $this->formatarMoedaCompacta($aciAnterior),
            ],
            'alertas' => [
                'total' => $alertas['total'],
                'zero_resposta' => $alertas['zero_resposta'],
                'sem_repasse' => $alertas['sem_repasse'],
                'zero_resposta_anterior' => $alertasAnterior['zero_resposta'],
                'sem_repasse_anterior' => $alertasAnterior['sem_repasse'],
            ],
            'qualidade' => [
                'percentual' => $qualidade['percentual'],
                'variacao' => round($qualidade['percentual'] - $qualidadeAnterior['percentual'], 1),
            ],
        ];
    }

    public function qualidade(): array
    {
        $ano = (int) EstatisticaService::getAnoReferencia();
        $atual = $this->qualidadeParaAno($ano);
        $anterior = $this->qualidadeParaAno($ano - 1);
        $variacao = round($atual['percentual'] - $anterior['percentual'], 1);

        return [
            'labels' => ['Entregue', 'Pendente'],
            'datasets' => [[
                'label' => 'Formulários',
                'data' => [$atual['entregue'], $atual['pendente']],
                'backgroundColor' => ['#2E9E8F', '#DCE0F5'],
                'borderWidth' => 0,
            ]],
            'percentual' => $atual['percentual'],
            'variacao' => $variacao,
            'ano' => $ano,
        ];
    }

    public function rankingUrgencia(int $limite = 5): array
    {
        $ano = (int) EstatisticaService::getAnoReferencia();
        $sinodais = $this->querySinodais()
            ->where('status', true)
            ->with('diretoria')
            ->get();

        return $sinodais
            ->map(function (Sinodal $sinodal) use ($ano) {
                return $this->metricasSinodal($sinodal, $ano);
            })
            ->filter(function (array $item) {
                return $item['status'] !== 'completo';
            })
            ->sortBy([
                fn (array $item) => $item['zero_resposta'] ? 0 : 1,
                fn (array $item) => $item['percentual'],
                fn (array $item) => $item['sem_repasse'] ? 0 : 1,
                fn (array $item) => $item['sem_lider'] ? 0 : 1,
            ])
            ->take($limite)
            ->values()
            ->all();
    }

    public static function whatsappSinodal(Sinodal $sinodal, int $ano, array $demanda = []): ?string
    {
        $diretoria = $sinodal->diretoria;
        $telefone = $diretoria?->contato_presidente ?: $diretoria?->contato_secretario_executivo;
        $digits = self::normalizarTelefone($telefone);
        if (!$digits) {
            return null;
        }

        $texto = self::mensagemLembrar($sinodal->nome, $ano, $demanda);

        return 'https://api.whatsapp.com/send?phone=' . $digits . '&text=' . rawurlencode($texto);
    }

    public static function normalizarTelefone(?string $telefone): ?string
    {
        if (empty($telefone)) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $telefone);
        if (strlen($digits) < 10) {
            return null;
        }
        if (!str_starts_with($digits, '55')) {
            $digits = '55' . $digits;
        }

        return $digits;
    }

    public static function parseValorAci(mixed $valor): float
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }
        if (is_int($valor) || is_float($valor)) {
            return (float) $valor;
        }
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        return ValorAciAno::parseValor((string) $valor);
    }

    private function metricasSinodal(Sinodal $sinodal, int $ano): array
    {
        $federacoes = $sinodal->federacoes()->where('status', true)->get();
        $totalFederacoes = $federacoes->count();
        $federacoesEntregues = 0;
        $totalLocais = 0;
        $locaisEntregues = 0;

        foreach ($federacoes as $federacao) {
            $relatorio = $federacao->relatorios()
                ->where('ano_referencia', $ano)
                ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
                ->first();
            if ($relatorio) {
                $federacoesEntregues++;
            }
            $locais = $federacao->locais()->where('status', true)->get();
            $totalLocais += $locais->count();
            foreach ($locais as $local) {
                if ($local->relatorios()->where('ano_referencia', $ano)->exists()) {
                    $locaisEntregues++;
                }
            }
        }

        $formularioSinodal = $sinodal->relatorios()
            ->where('ano_referencia', $ano)
            ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
            ->first();
        $aci = self::parseValorAci($formularioSinodal?->aci['valor_repassado'] ?? 0);
        $percentual = $totalLocais > 0 ? round(($locaisEntregues * 100) / $totalLocais, 1) : 0;
        $zeroResposta = $totalLocais > 0 && $locaisEntregues === 0;
        $semRepasse = $aci <= 0;
        $diretoria = $sinodal->diretoria;
        $semLider = empty($diretoria?->presidente) && empty($diretoria?->secretario_executivo);

        $status = 'pendente';
        if ($formularioSinodal && $totalFederacoes === $federacoesEntregues && $totalLocais > 0 && $locaisEntregues === $totalLocais) {
            $status = 'completo';
        } elseif ($formularioSinodal || $federacoesEntregues > 0 || $locaisEntregues > 0) {
            $status = 'parcial';
        }

        $demanda = [
            'zero_resposta' => $zeroResposta,
            'sem_repasse' => $semRepasse,
            'sem_lider' => $semLider,
        ];

        $meta = [];
        if ($zeroResposta) {
            $meta[] = '0% dos formulários entregues';
        } else {
            $meta[] = "{$percentual}% dos formulários entregues";
        }
        $meta[] = "{$federacoesEntregues}/{$totalFederacoes} Federações";
        if ($semLider) {
            $meta[] = 'sem líder cadastrado';
        } elseif ($semRepasse) {
            $meta[] = 'ACI não repassada';
        }

        return [
            'id' => $sinodal->id,
            'nome' => $sinodal->nome,
            'percentual' => $percentual,
            'federacoes' => "{$federacoesEntregues}/{$totalFederacoes}",
            'locais' => "{$locaisEntregues}/{$totalLocais}",
            'zero_resposta' => $zeroResposta,
            'sem_repasse' => $semRepasse,
            'sem_lider' => $semLider,
            'status' => $status,
            'severidade' => ($zeroResposta || $semLider) ? 'high' : 'mid',
            'meta' => implode(' · ', $meta),
            'whatsapp' => self::whatsappSinodal($sinodal, $ano, $demanda),
        ];
    }

    private function qualidadeParaAno(int $ano): array
    {
        $ids = $this->idsSinodais();
        if ($ids->isEmpty()) {
            return [
                'entregue' => 0,
                'pendente' => 0,
                'total' => 0,
                'percentual' => 0.0,
            ];
        }
        $quantidadeUmps = Local::whereIn('sinodal_id', $ids)
            ->where('status', true)
            ->whereHas('federacao', function ($q) {
                return $q->where('status', true);
            })
            ->count();

        $quantidadeFormularios = FormularioLocal::whereHas('local', function ($sql) use ($ids) {
                $sql->whereIn('sinodal_id', $ids)
                    ->where('status', true)
                    ->whereHas('federacao', function ($q) {
                        return $q->where('status', true);
                    });
            })
            ->where('ano_referencia', $ano)
            ->count();

        $restante = max(0, $quantidadeUmps - $quantidadeFormularios);
        $percentual = $quantidadeUmps > 0
            ? round(($quantidadeFormularios * 100) / $quantidadeUmps, 1)
            : 0.0;

        return [
            'entregue' => $quantidadeFormularios,
            'pendente' => $restante,
            'total' => $quantidadeUmps,
            'percentual' => $percentual,
        ];
    }

    private function totalSocios(int $ano): int
    {
        $ids = $this->idsSinodais();
        if ($ids->isEmpty()) {
            return 0;
        }
        $formularios = FormularioLocal::whereHas('local', function ($sql) use ($ids) {
                $sql->whereIn('sinodal_id', $ids);
            })
            ->where('ano_referencia', $ano)
            ->get();

        $total = 0;
        foreach ($formularios as $formulario) {
            $total += intval($formulario->perfil['ativos'] ?? 0)
                + intval($formulario->perfil['cooperadores'] ?? 0);
        }

        return $total;
    }

    private function serieSocios(int $ano): array
    {
        $serie = [];
        for ($i = 3; $i >= 0; $i--) {
            $serie[] = $this->totalSocios($ano - $i);
        }

        return $serie;
    }

    private function aciArrecadada(int $ano): float
    {
        $ids = $this->idsSinodais();
        if ($ids->isEmpty()) {
            return 0.0;
        }
        $formularios = FormularioSinodal::whereIn('sinodal_id', $ids)
            ->where('ano_referencia', $ano)
            ->get();

        $total = 0.0;
        foreach ($formularios as $formulario) {
            $total += self::parseValorAci($formulario->aci['valor_repassado'] ?? 0);
        }

        return $total;
    }

    private function aciMeta(int $ano): float
    {
        $ids = $this->idsSinodais();
        if ($ids->isEmpty()) {
            return 0.0;
        }
        $federacaoIds = Federacao::whereIn('sinodal_id', $ids)
            ->where('status', true)
            ->pluck('id');
        $totalSocios = FormularioFederacao::whereIn('federacao_id', $federacaoIds)
            ->where('ano_referencia', $ano)
            ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
            ->get()
            ->sum(fn (FormularioFederacao $formulario) => intval($formulario->perfil['ativos'] ?? 0));

        $valorAci = ValorAciAno::valorPara($ano);
        $minAci = floatval(optional(Parametro::where('nome', 'min_aci')->first())->valor) / 100;

        return $totalSocios * $valorAci * ComprovanteAciService::PORCENTAGEM_SINODAL * $minAci;
    }

    private function alertasParaAno(int $ano): array
    {
        $vazio = [
            'zero_resposta' => 0,
            'sem_repasse' => 0,
            'total' => 0,
        ];
        $ids = $this->idsSinodais();
        if ($ids->isEmpty()) {
            return $vazio;
        }
        $locaisPorSinodal = Local::whereIn('sinodal_id', $ids)
            ->where('status', true)
            ->whereHas('federacao', function ($q) {
                return $q->where('status', true);
            })
            ->selectRaw('sinodal_id, COUNT(*) as total')
            ->groupBy('sinodal_id')
            ->pluck('total', 'sinodal_id');

        $entreguesPorSinodal = FormularioLocal::query()
            ->selectRaw('locais.sinodal_id, COUNT(*) as total')
            ->join('locais', 'locais.id', '=', 'formularios_local_v1.local_id')
            ->whereIn('locais.sinodal_id', $ids)
            ->where('locais.status', true)
            ->where('formularios_local_v1.ano_referencia', $ano)
            ->whereNull('formularios_local_v1.deleted_at')
            ->whereNull('locais.deleted_at')
            ->whereHas('local.federacao', function ($q) {
                return $q->where('status', true);
            })
            ->groupBy('locais.sinodal_id')
            ->pluck('total', 'sinodal_id');

        $zeroResposta = 0;
        foreach ($ids as $id) {
            $totalLocais = (int) ($locaisPorSinodal[$id] ?? 0);
            $entregues = (int) ($entreguesPorSinodal[$id] ?? 0);
            if ($totalLocais > 0 && $entregues === 0) {
                $zeroResposta++;
            }
        }

        $semRepasse = 0;
        $formularios = FormularioSinodal::whereIn('sinodal_id', $ids)
            ->where('ano_referencia', $ano)
            ->get()
            ->keyBy('sinodal_id');

        foreach ($ids as $id) {
            $valor = self::parseValorAci(optional($formularios->get($id))->aci['valor_repassado'] ?? 0);
            if ($valor <= 0) {
                $semRepasse++;
            }
        }

        return [
            'zero_resposta' => $zeroResposta,
            'sem_repasse' => $semRepasse,
            'total' => $zeroResposta + $semRepasse,
        ];
    }

    private function querySinodais()
    {
        $query = Sinodal::query();
        if (!$this->nacional) {
            $query->where('regiao_id', auth()->user()->regiao_id);
        }

        return $query;
    }

    private function idsSinodais(): Collection
    {
        if ($this->idsSinodais === null) {
            $this->idsSinodais = $this->querySinodais()->where('status', true)->pluck('id');
        }

        return $this->idsSinodais;
    }

    private function variacaoPercentual(float $atual, float $anterior): float
    {
        if ($anterior <= 0) {
            return $atual > 0 ? 100.0 : 0.0;
        }

        return round((($atual - $anterior) * 100) / $anterior, 1);
    }

    private function crescimentoParaScore(float $variacao): float
    {
        $limitada = max(-20, min(20, $variacao));

        return (($limitada + 20) / 40) * 100;
    }

    private function rotuloSaude(int $score): string
    {
        if ($score >= 80) {
            return 'Saudável';
        }
        if ($score >= 50) {
            return 'Atenção moderada';
        }

        return 'Crítico';
    }

    private function corSaude(int $score): string
    {
        if ($score >= 80) {
            return 'var(--color-good)';
        }
        if ($score >= 50) {
            return 'var(--color-warn)';
        }

        return 'var(--color-bad)';
    }

    private function formatarVariacao(float $variacao): string
    {
        $seta = $variacao >= 0 ? '▲' : '▼';

        return $seta . ' ' . number_format(abs($variacao), 1, ',', '.') . '%';
    }

    private function formatarMoedaCompacta(float $valor): string
    {
        if ($valor >= 1000) {
            return 'R$ ' . number_format($valor / 1000, 1, ',', '.') . 'k';
        }

        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    private static function mensagemLembrar(string $nome, int $ano, array $demanda): string
    {
        if (!empty($demanda['zero_resposta'])) {
            return "Olá! Identificamos que a {$nome} ainda não entregou os formulários estatísticos de {$ano}. Precisamos da sua atenção para regularizar a entrega.";
        }
        if (!empty($demanda['sem_repasse'])) {
            return "Olá! A {$nome} ainda não registrou o repasse de ACI de {$ano}. Pedimos que verifique e atualize o formulário.";
        }
        if (!empty($demanda['sem_lider'])) {
            return "Olá! A {$nome} está sem presidente ou secretário executivo cadastrado na diretoria. Por favor, atualize os dados.";
        }

        return "Olá! Precisamos da sua atenção sobre a situação estatística da {$nome} no ano {$ano}.";
    }
}
