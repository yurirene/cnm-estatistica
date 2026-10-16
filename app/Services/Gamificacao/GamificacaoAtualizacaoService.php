<?php

namespace App\Services\Gamificacao;

use App\Models\Gamificacao\Auditoria;
use App\Models\Gamificacao\PilarResultado;
use App\Models\Gamificacao\Placar;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\Calculadores\CalculadorFactory;
use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;
use App\Services\Gamificacao\DTOs\ResultadoPlacar;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\OrigemAuditoria;
use App\Services\Gamificacao\Guards\EscritorPlacar;
use App\Services\Gamificacao\Guards\PlacarGuard;
use App\Services\Gamificacao\Regras\DescontoResolver;
use App\Services\Gamificacao\Regras\LigaResolver;
use App\Services\Gamificacao\Regras\ValoresLegaisPilar;
use App\Services\LogErroService;
use Illuminate\Support\Facades\DB;

class GamificacaoAtualizacaoService
{
    public function __construct(
        private readonly MontadorContexto $montador,
        private readonly CalculadorFactory $factory,
        private readonly PlacarGuard $guard,
        private readonly LigaResolver $ligaResolver,
        private readonly DescontoResolver $descontoResolver,
        private readonly ValoresLegaisPilar $valoresLegais,
        private readonly GamificacaoRankingService $ranking,
        private readonly GamificacaoConfiguracaoService $configuracao,
    ) {
    }

    public function recalcularSinodal(
        string $sinodalId,
        ?int $ano = null,
        OrigemAuditoria $origem = OrigemAuditoria::Formulario,
        bool $forcar = false
    ): Placar {
        $contexto = $this->montador->paraSinodal($sinodalId, $ano);

        return $this->persistir($contexto, $origem, $forcar);
    }

    public function recalcularFederacao(
        string $federacaoId,
        ?int $ano = null,
        OrigemAuditoria $origem = OrigemAuditoria::Formulario,
        bool $forcar = false
    ): Placar {
        $contexto = $this->montador->paraFederacao($federacaoId, $ano);

        return $this->persistir($contexto, $origem, $forcar);
    }

    public function recalcularTodos(?int $ano = null, ?NaturezaInstancia $natureza = null, bool $forcar = false): int
    {
        $this->configuracao->aplicar();
        $ano ??= EstatisticaService::getAnoReferencia();
        $atualizados = 0;

        if ($natureza === null || $natureza === NaturezaInstancia::Sinodal) {
            foreach (\App\Models\Sinodal::where('status', true)->pluck('id') as $id) {
                if ($this->recalcularInstanciaComSeguranca(
                    fn () => $this->recalcularSinodal($id, $ano, OrigemAuditoria::Comando, $forcar),
                    'sinodal',
                    (string) $id,
                    $ano
                )) {
                    $atualizados++;
                }
            }
        }

        if ($natureza === null || $natureza === NaturezaInstancia::Federacao) {
            foreach (\App\Models\Federacao::where('status', true)->pluck('id') as $id) {
                if ($this->recalcularInstanciaComSeguranca(
                    fn () => $this->recalcularFederacao($id, $ano, OrigemAuditoria::Comando, $forcar),
                    'federacao',
                    (string) $id,
                    $ano
                )) {
                    $atualizados++;
                }
            }
        }

        return $atualizados;
    }

    private function recalcularInstanciaComSeguranca(
        callable $recalcular,
        string $natureza,
        string $instanciaId,
        int $ano
    ): bool {
        try {
            $recalcular();

            return true;
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
                'natureza' => $natureza,
                'instancia_id' => $instanciaId,
                'ano' => $ano,
            ]);

            return false;
        }
    }

    private function persistir(ContextoInstancia $contexto, OrigemAuditoria $origem, bool $forcar): Placar
    {
        $this->guard->assertInstanciaValida($contexto);

        $resultado = $this->calcular($contexto);

        return EscritorPlacar::executar(function () use ($contexto, $resultado, $origem, $forcar) {
            return DB::transaction(function () use ($contexto, $resultado, $origem, $forcar) {
                $placar = $this->encontrarOuCriar($contexto);
                $this->guard->assertAnoAberto($placar, $forcar);

                $antes = $placar->exists ? $placar->only([
                    'liga', 'socios_ativos', 'pontos_ano', 'pontos_ciclo', 'desconto_projetado',
                ]) : [];

                $fechadoEm = $placar->fechado_em;
                if ($this->guard->deveFecharAno($contexto->ano) && $fechadoEm === null && ! $forcar) {
                    $fechadoEm = now();
                }

                $placar->forceFill([
                    'ciclo' => $contexto->ciclo,
                    'ano_referencia' => $contexto->ano,
                    'sinodal_id' => $contexto->sinodalId,
                    'federacao_id' => $contexto->federacaoId,
                    'liga' => $resultado->liga,
                    'socios_ativos' => $resultado->sociosAtivos,
                    'pontos_ano' => $resultado->pontosAno,
                    'pontos_ciclo' => $resultado->pontosCiclo,
                    'desconto_projetado' => $resultado->descontoProjetado,
                    'calculado_em' => now(),
                    'fechado_em' => $fechadoEm,
                ])->save();

                $this->sincronizarPilares($placar, $resultado->pilares());
                $this->auditar($placar, $antes, $origem);
                $this->ranking->recalcular($contexto->natureza, $resultado->liga, $contexto->ciclo, $contexto->ano);

                return $placar->refresh();
            });
        });
    }

    public function calcular(ContextoInstancia $contexto): ResultadoPlacar
    {
        $this->guard->assertInstanciaValida($contexto);
        $pilares = [];
        $soma = 0;

        foreach ($this->factory->para($contexto->natureza) as $calculador) {
            $resultado = $calculador->calcular($contexto);
            $this->guard->assertPilarDaNatureza($contexto->natureza, $resultado->pilar);
            $pontos = $this->guard->sanitizarPontos($resultado);
            $pilares[] = new ResultadoPilar(
                pilar: $resultado->pilar,
                pontos: $pontos,
                pontosMaximo: $resultado->pontosMaximo,
                progresso: $resultado->progresso,
                status: $resultado->status,
                rotuloStatus: $resultado->rotuloStatus,
                rodape: $resultado->rodape,
                ctaLabel: $resultado->ctaLabel,
                ctaRota: $resultado->ctaRota,
                hint: $resultado->hint,
                detalhes: $resultado->detalhes,
            );
            $soma += $pontos;
        }

        $pontosAno = $this->valoresLegais->limitarTeto($contexto->natureza, $soma);
        $pontosCiclo = $this->somarCiclo($contexto, $pontosAno);
        $liga = $this->ligaResolver->resolver($contexto->natureza, $contexto->sociosAtivos);

        return new ResultadoPlacar(
            natureza: $contexto->natureza,
            liga: $liga,
            faixaLiga: $this->ligaResolver->faixa($contexto->natureza, $liga),
            sociosAtivos: $contexto->sociosAtivos,
            pontosAno: $pontosAno,
            tetoAno: $contexto->natureza->tetoAnual(),
            pontosCiclo: $pontosCiclo,
            tetoCiclo: $contexto->natureza->tetoCiclo(),
            descontoProjetado: $this->descontoResolver->projetar($contexto->natureza, $pontosCiclo),
            pontosParaProximoDesconto: $this->descontoResolver->pontosParaProximo($contexto->natureza, $pontosCiclo),
            pilares: $pilares,
            ano: $contexto->ano,
            ciclo: $contexto->ciclo,
            sinodalId: $contexto->sinodalId,
            federacaoId: $contexto->federacaoId,
        );
    }

    private function encontrarOuCriar(ContextoInstancia $contexto): Placar
    {
        $existente = Placar::withoutGlobalScopes()
            ->where('ciclo', $contexto->ciclo)
            ->where('ano_referencia', $contexto->ano)
            ->when(
                $contexto->sinodalId,
                fn ($q) => $q->where('sinodal_id', $contexto->sinodalId),
                fn ($q) => $q->where('federacao_id', $contexto->federacaoId)
            )
            ->first();

        if ($existente) {
            return $existente;
        }

        $novo = new Placar();
        $novo->forceFill([
            'ciclo' => $contexto->ciclo,
            'ano_referencia' => $contexto->ano,
            'sinodal_id' => $contexto->sinodalId,
            'federacao_id' => $contexto->federacaoId,
            'liga' => $this->ligaResolver->resolver($contexto->natureza, $contexto->sociosAtivos),
            'socios_ativos' => $contexto->sociosAtivos,
            'pontos_ano' => 0,
            'pontos_ciclo' => 0,
            'posicao_liga' => 0,
            'total_na_liga' => 0,
            'desconto_projetado' => 0,
        ]);
        $novo->save();

        return $novo;
    }

    /** @param ResultadoPilar[] $pilares */
    private function sincronizarPilares(Placar $placar, array $pilares): void
    {
        foreach ($pilares as $pilar) {
            $registro = PilarResultado::firstOrNew([
                'placar_id' => $placar->id,
                'pilar' => $pilar->pilar->value,
            ]);
            $registro->forceFill([
                'pontos' => $pilar->pontos,
                'pontos_maximo' => $pilar->pontosMaximo,
                'progresso' => min(100, max(0, $pilar->progresso)),
                'status' => $pilar->status,
                'detalhes' => array_merge($pilar->detalhes, [
                    'rotulo_status' => $pilar->rotuloStatus,
                    'rodape' => $pilar->rodape,
                    'cta_label' => $pilar->ctaLabel,
                    'cta_rota' => $pilar->ctaRota,
                    'hint' => $pilar->hint,
                ]),
            ]);
            $registro->save();
        }
    }

    private function somarCiclo(ContextoInstancia $contexto, int $pontosAnoAtual): int
    {
        $anteriores = (int) Placar::withoutGlobalScopes()
            ->where('ciclo', $contexto->ciclo)
            ->where('ano_referencia', '!=', $contexto->ano)
            ->when(
                $contexto->sinodalId,
                fn ($q) => $q->where('sinodal_id', $contexto->sinodalId),
                fn ($q) => $q->where('federacao_id', $contexto->federacaoId)
            )
            ->sum('pontos_ano');

        return $anteriores + $pontosAnoAtual;
    }

    private function auditar(Placar $placar, array $antes, OrigemAuditoria $origem): void
    {
        $depois = $placar->only([
            'liga', 'socios_ativos', 'pontos_ano', 'pontos_ciclo', 'desconto_projetado',
        ]);

        if ($antes === $depois) {
            return;
        }

        Auditoria::create([
            'placar_id' => $placar->id,
            'pilar' => null,
            'antes' => $antes ?: null,
            'depois' => $depois,
            'motivo' => 'Recálculo do placar anual',
            'origem' => $origem,
        ]);
    }
}
