<?php

namespace App\Services\EstatisticaInteligente;

use App\Models\Estatistica\EstatisticaGeral;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Regiao;
use App\Models\Sinodal;
use App\Services\Estatistica\EstatisticaService;
use App\Services\EstatisticaInteligente\Calculo\AnomaliaDetector;
use App\Services\EstatisticaInteligente\Calculo\IndicadorDerivador;
use App\Services\EstatisticaInteligente\Calculo\PercentilCalculator;
use App\Services\EstatisticaInteligente\Calculo\PorteClassifier;
use App\Services\EstatisticaInteligente\Calculo\QualidadeCalculator;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;

final class MontadorContextoService
{
    public function __construct(
        private readonly IndicadorDerivador $derivador,
        private readonly PercentilCalculator $percentil,
        private readonly PorteClassifier $portes,
        private readonly QualidadeCalculator $qualidade,
        private readonly AnomaliaDetector $anomalias,
    ) {
    }

    public function montar(NivelEstatisticoEnum $nivel, string $nivelId, int $ano): ?ContextoIaDTO
    {
        $atual = $this->carregarSnapshot($nivel, $nivelId, $ano);
        if ($atual === null) {
            return null;
        }

        $anterior = $this->carregarSnapshot($nivel, $nivelId, $ano - 1);
        $anterior2 = $this->carregarSnapshot($nivel, $nivelId, $ano - 2);

        $pares = $this->pares($nivel, $nivelId, $ano, (int) ($atual['perfil']['ativos'] ?? 0));
        $paresAnterior = $anterior === null
            ? ['valores' => [], 'grupo' => $pares['grupo']]
            : $this->pares($nivel, $nivelId, $ano - 1, (int) ($anterior['perfil']['ativos'] ?? 0));

        return $this->montarDeDados(
            $nivel,
            $nivelId,
            $ano,
            $atual,
            $anterior,
            $anterior2,
            $pares['valores'],
            $paresAnterior['valores'],
            $pares['grupo'],
        );
    }

    /**
     * @param  array{perfil: array<string, mixed>, programacoes: array<string, mixed>}  $atual
     * @param  array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null  $anterior
     * @param  array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null  $anterior2
     * @param  array<int, int|float>  $paresAtuais
     * @param  array<int, int|float>  $paresAnteriores
     */
    public function montarDeDados(
        NivelEstatisticoEnum $nivel,
        string $nivelId,
        int $ano,
        array $atual,
        ?array $anterior,
        ?array $anterior2,
        array $paresAtuais,
        array $paresAnteriores,
        string $grupoPares,
    ): ContextoIaDTO {
        $indicadores = $this->derivador->derivar(
            $atual['perfil'],
            $atual['programacoes'],
            $anterior['perfil'] ?? null,
            $anterior['programacoes'] ?? null,
        );

        $historico = [];
        if ($anterior !== null) {
            $historico[$ano - 1] = $this->derivador->derivar(
                $anterior['perfil'],
                $anterior['programacoes'],
                $anterior2['perfil'] ?? null,
                $anterior2['programacoes'] ?? null,
            );
        }
        if ($anterior2 !== null) {
            $historico[$ano - 2] = $this->derivador->derivar(
                $anterior2['perfil'],
                $anterior2['programacoes'],
                null,
                null,
            );
        }

        $crescimento = is_numeric($indicadores['crescimento_ativos_anual'] ?? null)
            ? (float) $indicadores['crescimento_ativos_anual']
            : null;
        $anomalias = $this->anomalias->detectar($crescimento);
        $score = $this->qualidade->score($atual['perfil'], $anterior !== null, $anomalias);

        $comparativo = null;
        $percentilAtual = $this->percentil->calcular((float) $indicadores['membros_ativos'], $paresAtuais);
        if ($percentilAtual !== null) {
            $comparativo = [
                'percentil' => $percentilAtual,
                'grupo_pares' => $grupoPares,
            ];
            $percentilAnterior = $anterior !== null
                ? $this->percentil->calcular((float) ($anterior['perfil']['ativos'] ?? 0), $paresAnteriores)
                : null;
            if ($percentilAnterior !== null) {
                $comparativo['percentil_anterior'] = $percentilAnterior;
            }
        }

        return ContextoIaDTO::fromArray([
            'periodo' => $ano,
            'nivel' => $nivel->value,
            'organizacao_id' => $nivelId !== '' ? $nivelId : null,
            'indicadores' => $indicadores,
            'historico' => $historico,
            'comparativo' => $comparativo,
            'anomalias' => $anomalias,
            'qualidade' => ['score' => $score],
        ]);
    }

    /**
     * @return array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null
     */
    private function carregarSnapshot(NivelEstatisticoEnum $nivel, string $nivelId, int $ano): ?array
    {
        return match ($nivel) {
            NivelEstatisticoEnum::Local => $this->snapshotLocal($nivelId, $ano),
            NivelEstatisticoEnum::Federacao => $this->snapshotFederacao($nivelId, $ano),
            NivelEstatisticoEnum::Sinodal => $this->snapshotSinodal($nivelId, $ano),
            NivelEstatisticoEnum::Regiao => $this->snapshotAgregado($ano, (int) $nivelId),
            NivelEstatisticoEnum::Nacional => $this->snapshotNacional($ano),
        };
    }

    /**
     * @return array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null
     */
    private function snapshotLocal(string $localId, int $ano): ?array
    {
        $formulario = FormularioLocal::query()
            ->where('local_id', $localId)
            ->where('ano_referencia', $ano)
            ->first();

        if ($formulario === null) {
            return null;
        }

        return [
            'perfil' => (array) $formulario->perfil,
            'programacoes' => (array) $formulario->programacoes,
        ];
    }

    /**
     * @return array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null
     */
    private function snapshotFederacao(string $federacaoId, int $ano): ?array
    {
        $formulario = FormularioFederacao::query()
            ->where('federacao_id', $federacaoId)
            ->where('ano_referencia', $ano)
            ->first();

        if ($formulario === null) {
            return null;
        }

        return [
            'perfil' => (array) $formulario->perfil,
            'programacoes' => (array) ($formulario->programacoes_locais ?: $formulario->programacoes),
        ];
    }

    /**
     * @return array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null
     */
    private function snapshotSinodal(string $sinodalId, int $ano): ?array
    {
        $formulario = FormularioSinodal::query()
            ->where('sinodal_id', $sinodalId)
            ->where('ano_referencia', $ano)
            ->first();

        if ($formulario === null) {
            return null;
        }

        return [
            'perfil' => (array) $formulario->perfil,
            'programacoes' => (array) ($formulario->programacoes_locais ?: $formulario->programacoes),
        ];
    }

    /**
     * @return array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null
     */
    private function snapshotNacional(int $ano): ?array
    {
        $geral = EstatisticaGeral::query()->where('ano_referencia', $ano)->first();
        if ($geral !== null && is_array($geral->perfil) && $geral->perfil !== []) {
            return [
                'perfil' => (array) $geral->perfil,
                'programacoes' => (array) $geral->programacoes_locais,
            ];
        }

        return $this->snapshotAgregado($ano, null);
    }

    /**
     * @return array{perfil: array<string, mixed>, programacoes: array<string, mixed>}|null
     */
    private function snapshotAgregado(int $ano, ?int $regiaoId): ?array
    {
        $dados = EstatisticaService::getDadosRelatorioGeral($ano, $regiaoId);
        if ((int) ($dados['abrangencia']['locais']['respondido'] ?? 0) === 0) {
            return null;
        }

        return [
            'perfil' => (array) ($dados['perfil'] ?? []),
            'programacoes' => (array) ($dados['programacoes']['locais'] ?? []),
        ];
    }

    /**
     * @return array{valores: array<int, int>, grupo: string}
     */
    private function pares(NivelEstatisticoEnum $nivel, string $nivelId, int $ano, int $ativos): array
    {
        $minimo = (int) config('estatistica_regras.pares_minimo', 3);

        return match ($nivel) {
            NivelEstatisticoEnum::Local => $this->paresLocal($nivelId, $ano, $ativos, $minimo),
            NivelEstatisticoEnum::Federacao => $this->paresFederacao($nivelId, $ano, $ativos, $minimo),
            NivelEstatisticoEnum::Sinodal => $this->paresSinodal($nivelId, $ano),
            NivelEstatisticoEnum::Regiao => $this->paresRegiao($ano),
            NivelEstatisticoEnum::Nacional => ['valores' => [], 'grupo' => ''],
        };
    }

    /**
     * @return array{valores: array<int, int>, grupo: string}
     */
    private function paresLocal(string $localId, int $ano, int $ativos, int $minimo): array
    {
        $local = Local::query()->find($localId);
        $valores = [];
        if ($local?->federacao_id) {
            $valores = $this->ativosLocais($ano, function ($query) use ($local) {
                $query->where('federacao_id', $local->federacao_id);
            });
        }

        if (count($valores) >= $minimo) {
            return ['valores' => $valores, 'grupo' => 'das UMPs da mesma federação'];
        }

        $codigo = $this->portes->classificar($ativos);
        $intervalo = $codigo ? $this->portes->intervalo($codigo) : null;
        if ($intervalo === null) {
            return ['valores' => $valores, 'grupo' => 'das organizações de referência'];
        }

        $porte = $this->ativosLocais($ano, null, $intervalo);

        return ['valores' => $porte, 'grupo' => 'das organizações de porte semelhante'];
    }

    /**
     * @return array{valores: array<int, int>, grupo: string}
     */
    private function paresFederacao(string $federacaoId, int $ano, int $ativos, int $minimo): array
    {
        $federacao = Federacao::query()->find($federacaoId);
        $valores = [];
        if ($federacao?->sinodal_id) {
            $valores = $this->ativosFederacoes($ano, $federacao->sinodal_id);
        }

        if (count($valores) >= $minimo) {
            return ['valores' => $valores, 'grupo' => 'das federações da mesma sinodal'];
        }

        $codigo = $this->portes->classificar($ativos);
        $intervalo = $codigo ? $this->portes->intervalo($codigo) : null;
        if ($intervalo === null) {
            return ['valores' => $valores, 'grupo' => 'das organizações de referência'];
        }

        return [
            'valores' => $this->ativosFederacoes($ano, null, $intervalo),
            'grupo' => 'das organizações de porte semelhante',
        ];
    }

    /**
     * @return array{valores: array<int, int>, grupo: string}
     */
    private function paresSinodal(string $sinodalId, int $ano): array
    {
        $sinodal = Sinodal::query()->find($sinodalId);
        $regiaoId = $sinodal?->regiao_id;
        $valores = [];

        $query = FormularioSinodal::query()->where('ano_referencia', $ano);
        if ($regiaoId) {
            $query->whereHas('sinodal', function ($q) use ($regiaoId) {
                $q->where('regiao_id', $regiaoId)->where('status', true);
            });
        }

        foreach ($query->get(['perfil']) as $formulario) {
            $valores[] = (int) (($formulario->perfil['ativos'] ?? 0));
        }

        return ['valores' => $valores, 'grupo' => 'das sinodais da mesma região'];
    }

    /**
     * @return array{valores: array<int, int>, grupo: string}
     */
    private function paresRegiao(int $ano): array
    {
        $valores = [];
        foreach (Regiao::query()->pluck('id') as $regiaoId) {
            $snapshot = $this->snapshotAgregado($ano, (int) $regiaoId);
            if ($snapshot !== null) {
                $valores[] = (int) ($snapshot['perfil']['ativos'] ?? 0);
            }
        }

        return ['valores' => $valores, 'grupo' => 'das regiões'];
    }

    /**
     * @param  callable|null  $filtroLocal
     * @param  array{min: int, max: int|null}|null  $intervalo
     * @return array<int, int>
     */
    private function ativosLocais(int $ano, ?callable $filtroLocal, ?array $intervalo = null): array
    {
        $query = FormularioLocal::query()
            ->where('ano_referencia', $ano)
            ->whereHas('local', function ($q) use ($filtroLocal) {
                $q->where('status', true);
                if ($filtroLocal !== null) {
                    $filtroLocal($q);
                }
            });

        $valores = [];
        foreach ($query->get(['perfil']) as $formulario) {
            $ativos = (int) ($formulario->perfil['ativos'] ?? 0);
            if ($intervalo !== null) {
                if ($ativos < $intervalo['min']) {
                    continue;
                }
                if ($intervalo['max'] !== null && $ativos > $intervalo['max']) {
                    continue;
                }
            }
            $valores[] = $ativos;
        }

        return $valores;
    }

    /**
     * @param  array{min: int, max: int|null}|null  $intervalo
     * @return array<int, int>
     */
    private function ativosFederacoes(int $ano, ?string $sinodalId, ?array $intervalo = null): array
    {
        $query = FormularioFederacao::query()
            ->where('ano_referencia', $ano)
            ->whereHas('federacao', function ($q) use ($sinodalId) {
                $q->where('status', true);
                if ($sinodalId !== null) {
                    $q->where('sinodal_id', $sinodalId);
                }
            });

        $valores = [];
        foreach ($query->get(['perfil']) as $formulario) {
            $ativos = (int) ($formulario->perfil['ativos'] ?? 0);
            if ($intervalo !== null) {
                if ($ativos < $intervalo['min']) {
                    continue;
                }
                if ($intervalo['max'] !== null && $ativos > $intervalo['max']) {
                    continue;
                }
            }
            $valores[] = $ativos;
        }

        return $valores;
    }
}
