<?php

namespace App\Services\Gamificacao;

use App\Models\Gamificacao\Placar;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\Calculadores\EvangelismoCalculador;
use App\Services\Gamificacao\DTOs\PainelInicioDTO;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\OrigemAuditoria;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;

class GamificacaoConsultaService
{
    public function __construct(
        private readonly GamificacaoAtualizacaoService $atualizacao,
        private readonly CalendarioGamificacao $calendario,
    ) {
    }

    public function painelDoUsuarioLogado(): ?PainelInicioDTO
    {
        if (Gate::check('sinodal')) {
            $id = auth()->user()->sinodal_id;
            if (! $id) {
                return null;
            }

            return $this->painel(NaturezaInstancia::Sinodal, $id);
        }

        if (Gate::check('federacao')) {
            $id = auth()->user()->federacao_id;
            if (! $id) {
                return null;
            }

            return $this->painel(NaturezaInstancia::Federacao, $id);
        }

        return null;
    }

    public function painel(NaturezaInstancia $natureza, string $instanciaId, ?int $ano = null): PainelInicioDTO
    {
        $this->assertAcesso($natureza, $instanciaId);
        $ano ??= EstatisticaService::getAnoReferencia();
        $ciclo = $this->calendario->ciclo();

        $placar = $this->buscarPlacar($natureza, $instanciaId, $ciclo, $ano);

        if (! $placar) {
            try {
                $placar = $natureza === NaturezaInstancia::Sinodal
                    ? $this->atualizacao->recalcularSinodal($instanciaId, $ano, OrigemAuditoria::Formulario)
                    : $this->atualizacao->recalcularFederacao($instanciaId, $ano, OrigemAuditoria::Formulario);
            } catch (\Throwable) {
                return PainelInicioDTO::vazio($natureza->value);
            }
        }

        $placar->load('pilares');

        return $this->montarDto($natureza, $placar);
    }

    private function buscarPlacar(
        NaturezaInstancia $natureza,
        string $instanciaId,
        string $ciclo,
        int $ano
    ): ?Placar {
        return Placar::query()
            ->where('ciclo', $ciclo)
            ->where('ano_referencia', $ano)
            ->when(
                $natureza === NaturezaInstancia::Sinodal,
                fn ($q) => $q->where('sinodal_id', $instanciaId),
                fn ($q) => $q->where('federacao_id', $instanciaId)
            )
            ->first();
    }

    private function montarDto(NaturezaInstancia $natureza, Placar $placar): PainelInicioDTO
    {
        $pilares = $placar->pilares->map(function ($pilar) {
            $detalhes = $pilar->detalhes ?? [];
            $ehEvangelismo = $pilar->pilar === Pilar::Evangelismo;
            $rodape = $detalhes['rodape'] ?? '';
            if ($ehEvangelismo && str_contains($rodape, '60%=')) {
                $rodape = explode(' · ', $rodape)[0];
            }

            return [
                'pilar' => $pilar->pilar->value,
                'label' => $pilar->pilar->label(),
                'pontos' => $pilar->pontos,
                'pontos_maximo' => $pilar->pontos_maximo,
                'progresso' => $pilar->progresso,
                'status' => $pilar->status->value,
                'rotulo_status' => $detalhes['rotulo_status'] ?? $pilar->status->label(),
                'rodape' => $rodape,
                'cta_label' => $ehEvangelismo ? null : ($detalhes['cta_label'] ?? null),
                'cta_rota' => $ehEvangelismo ? null : ($detalhes['cta_rota'] ?? null),
                'hint' => $ehEvangelismo
                    ? ($detalhes['hint'] ?? EvangelismoCalculador::hint())
                    : ($detalhes['hint'] ?? null),
                'cor' => $pilar->pilar->cor(),
                'detalhes' => $detalhes,
            ];
        })->values()->all();

        $segmentos = collect($pilares)->map(fn (array $pilar) => [
            'chave' => $pilar['pilar'],
            'label' => $pilar['label'],
            'pontos' => $pilar['pontos'],
            'max' => $pilar['pontos_maximo'],
            'cor' => $pilar['cor'],
        ])->all();

        $avisos = [];
        foreach ($pilares as $pilar) {
            if (in_array($pilar['status'], [StatusPilar::Risco->value, StatusPilar::Pendente->value], true)
                && $pilar['pontos'] === 0
                && $pilar['pontos_maximo'] > 0
            ) {
                $avisos[] = [
                    'titulo' => $pilar['label'],
                    'texto' => $pilar['rodape'],
                    'pts' => $pilar['pontos_maximo'],
                    'tipo' => $pilar['status'] === StatusPilar::Risco->value ? 'risco' : 'aviso',
                ];
            }
        }

        $estatistica = collect($pilares)->firstWhere('pilar', Pilar::Estatistica->value);
        $tendencia = $this->tendencia($placar, $pilares);
        $tetoAno = $natureza->tetoAnual();
        $tetoCiclo = $natureza->tetoCiclo();
        $faixa = (string) GamificacaoConfiguracaoService::get("ligas.{$natureza->value}.{$placar->liga->value}.faixa", '');

        return new PainelInicioDTO(
            ciclo: $placar->ciclo,
            ano: $placar->ano_referencia,
            anoGamificacao: $this->calendario->anoGamificacao($placar->ano_referencia),
            natureza: $natureza->value,
            liga: $placar->liga->value,
            ligaLabel: $placar->liga->label(),
            faixaLiga: $faixa,
            sociosAtivos: $placar->socios_ativos,
            posicao: $placar->posicao_liga,
            totalNaLiga: $placar->total_na_liga,
            pontosAno: $placar->pontos_ano,
            tetoAno: $tetoAno,
            pontosCiclo: $placar->pontos_ciclo,
            tetoCiclo: $tetoCiclo,
            descontoProjetado: $placar->desconto_projetado,
            pontosParaProximoDesconto: max(
                0,
                GamificacaoConfiguracaoService::getInt("descontos.{$natureza->value}.50") - $placar->pontos_ciclo
            ),
            tendenciaRanking: $tendencia,
            segmentos: $segmentos,
            pilares: $pilares,
            avisosPontos: $avisos,
            percentualEntrega: (float) data_get($estatistica, 'detalhes.percentual', $estatistica['progresso'] ?? 0),
            totalFilhos: (int) data_get($estatistica, 'detalhes.total', 0),
            filhosEntregaram: (int) data_get($estatistica, 'detalhes.entregues', 0),
            metaEntrega: GamificacaoConfiguracaoService::getInt('pilares.estatistica.minimo_percentual', 80),
        );
    }

    private function tendencia(Placar $placar, array $pilares): string
    {
        $emRisco = collect($pilares)
            ->filter(fn (array $p) => $p['status'] === StatusPilar::Risco->value)
            ->count();

        if ($emRisco > 0) {
            return "Para subir na liga: pontue nos {$emRisco} pilares em risco antes do prazo.";
        }

        if ($placar->posicao_liga <= 1) {
            return 'Você está no topo da liga. Mantenha a pontuação até o fim do ciclo.';
        }

        return 'Continue pontuando nos pilares para subir na liga.';
    }

    private function assertAcesso(NaturezaInstancia $natureza, string $instanciaId): void
    {
        if (! auth()->check()) {
            return;
        }

        $usuario = auth()->user();
        if ($usuario->admin) {
            return;
        }

        $campo = UserService::getCampoInstanciaDB();
        if ($natureza === NaturezaInstancia::Sinodal
            && ($usuario->role->name ?? null) === User::ROLE_SINODAL
            && $campo['id'] !== $instanciaId
        ) {
            abort(403);
        }

        if ($natureza === NaturezaInstancia::Federacao
            && ($usuario->role->name ?? null) === User::ROLE_FEDERACAO
            && $campo['id'] !== $instanciaId
        ) {
            abort(403);
        }
    }
}
