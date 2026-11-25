<?php

namespace App\Services\Gamificacao;

use App\Helpers\FormHelper;
use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\ComissaoExecutiva\Reuniao;
use App\Models\ComprovanteACI;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Gamificacao\Conquista;
use App\Models\Local;
use App\Models\Sinodal;
use App\Models\Federacao;
use App\Models\ValorAciAno;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\Gamificacao\Regras\JurisdicaoExercicioResolver;
use App\Services\Gamificacao\Regras\PontuadorEventos;
use Carbon\Carbon;

class MontadorContexto
{
    public function __construct(
        private readonly CalendarioGamificacao $calendario,
        private readonly PontuadorEventos $pontuadorEventos,
        private readonly JurisdicaoExercicioResolver $jurisdicao,
    ) {
    }

    public function paraSinodal(string $sinodalId, ?int $ano = null): ContextoInstancia
    {
        $ano ??= EstatisticaService::getAnoReferencia();
        $sinodal = Sinodal::findOrFail($sinodalId);
        $locais = $this->jurisdicao->locaisDaSinodal($sinodalId, $ano);
        $localIds = $locais->pluck('id');

        $entregues = FormularioLocal::whereIn('local_id', $localIds)
            ->where('ano_referencia', $ano)
            ->get();

        $totalFilhos = $locais->count();
        $filhosEntregaram = $entregues->count();
        $percentual = $totalFilhos > 0 ? ($filhosEntregaram * 100) / $totalFilhos : 0;

        $sociosAtivos = $this->sociosAtivosSinodal($ano, $locais);
        $sociosParaLiga = $this->sociosAtivosSinodal(
            $ano - 1,
            $this->jurisdicao->locaisDaSinodal($sinodalId, $ano - 1)
        );
        $evangelismo = $this->contarProgramacoes($localIds, $ano);

        $formularioSinodal = FormularioSinodal::where('sinodal_id', $sinodalId)
            ->where('ano_referencia', $ano)
            ->first();
        $aci = $this->extrairAci($formularioSinodal?->aci, 'valor_repassado');
        $previsto = $this->valorPrevistoAci($sociosAtivos, true, $ano);
        $comprovante = ComprovanteACI::where('sinodal_id', $sinodalId)
            ->where('ano', $ano)
            ->exists();
        $metaAtingida = ComprovanteACI::where('sinodal_id', $sinodalId)
            ->where('ano', $ano)
            ->where('status', ComprovanteACI::STATUS_META_ATINGIDA)
            ->exists();

        $ceConfirmada = DelegadoComissaoExecutiva::where('sinodal_id', $sinodalId)
            ->where('status', DelegadoComissaoExecutiva::STATUS_PRESENTE)
            ->whereHas('reuniao', fn ($q) => $q->where('ano', $ano))
            ->exists();

        if (! $ceConfirmada) {
            $reuniaoAberta = Reuniao::where('ano', $ano)->where('aberto', 1)->exists();
            $ceConfirmada = $reuniaoAberta && DelegadoComissaoExecutiva::where('sinodal_id', $sinodalId)
                ->where('status', DelegadoComissaoExecutiva::STATUS_PRESENTE)
                ->whereHas('reuniao', fn ($q) => $q->where('ano', $ano))
                ->exists();
        }

        $conquistas = $this->conquistas(NaturezaInstancia::Sinodal, $sinodalId, null, $ano);

        return new ContextoInstancia(
            natureza: NaturezaInstancia::Sinodal,
            sinodalId: $sinodalId,
            federacaoId: null,
            ano: $ano,
            ciclo: $this->calendario->ciclo(),
            sociosAtivos: $sociosAtivos,
            sociosParaLiga: $sociosParaLiga,
            totalFilhos: $totalFilhos,
            filhosEntregaram: $filhosEntregaram,
            percentualEstatistica: $percentual,
            aciComprovanteAnexado: $comprovante,
            aciRepasseInformado: data_get($formularioSinodal?->aci, 'repasse') === 'S',
            aciValorRepassado: $aci,
            aciValorPrevisto: $previsto,
            umpsTotal: $evangelismo['total'],
            umpsComProgramacao: $evangelismo['com_programacao'],
            percentualEvangelismo: $evangelismo['percentual'],
            ceConfirmada: $ceConfirmada,
            temConexaoMissionaria: $conquistas['conexao'],
            dataEntregaEstatistica: $this->dataMaioriaEntrega($entregues, $totalFilhos),
            pontosEventosPorTipo: [],
            aciMetaAtingida: $metaAtingida,
            instanciaNome: $sinodal->nome,
        );
    }

    public function paraFederacao(string $federacaoId, ?int $ano = null): ContextoInstancia
    {
        $ano ??= EstatisticaService::getAnoReferencia();
        $federacao = Federacao::findOrFail($federacaoId);
        $locais = $this->jurisdicao->locaisDaFederacao($federacaoId, $ano);
        $localIds = $locais->pluck('id');

        $entregues = FormularioLocal::whereIn('local_id', $localIds)
            ->where('ano_referencia', $ano)
            ->get();

        $totalFilhos = $locais->count();
        $filhosEntregaram = $entregues->count();
        $percentual = $totalFilhos > 0 ? ($filhosEntregaram * 100) / $totalFilhos : 0;

        $formulario = FormularioFederacao::where('federacao_id', $federacaoId)
            ->where('ano_referencia', $ano)
            ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
            ->first();

        $sociosAtivos = (int) data_get($formulario?->perfil, 'ativos', 0);
        $sociosParaLiga = $this->sociosAtivosFederacao($federacaoId, $ano - 1);
        $evangelismo = $this->contarProgramacoes($localIds, $ano);
        $aci = $this->extrairAci($formulario?->aci, 'valor');
        $previsto = $this->valorPrevistoAci($sociosAtivos, false, $ano);
        $conquistas = $this->conquistas(NaturezaInstancia::Federacao, null, $federacaoId, $ano);

        return new ContextoInstancia(
            natureza: NaturezaInstancia::Federacao,
            sinodalId: null,
            federacaoId: $federacaoId,
            ano: $ano,
            ciclo: $this->calendario->ciclo(),
            sociosAtivos: $sociosAtivos,
            sociosParaLiga: $sociosParaLiga,
            totalFilhos: $totalFilhos,
            filhosEntregaram: $filhosEntregaram,
            percentualEstatistica: $percentual,
            aciComprovanteAnexado: true,
            aciRepasseInformado: data_get($formulario?->aci, 'repasse') === 'S',
            aciValorRepassado: $aci,
            aciValorPrevisto: $previsto,
            umpsTotal: $evangelismo['total'],
            umpsComProgramacao: $evangelismo['com_programacao'],
            percentualEvangelismo: $evangelismo['percentual'],
            ceConfirmada: false,
            temConexaoMissionaria: false,
            dataEntregaEstatistica: $this->dataLimite80Percent($entregues, $totalFilhos),
            pontosEventosPorTipo: $conquistas['eventos'],
            instanciaNome: $federacao->nome,
        );
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Local>  $locaisDoExercicio
     */
    private function sociosAtivosSinodal(int $ano, $locaisDoExercicio): int
    {
        $federacaoIds = $locaisDoExercicio
            ->map(fn (Local $local) => $this->jurisdicao->vinculoDoLocal($local, $ano)['federacao_id'])
            ->filter()
            ->unique()
            ->values();

        if ($federacaoIds->isEmpty()) {
            return 0;
        }

        return (int) FormularioFederacao::whereIn('federacao_id', $federacaoIds)
            ->where('ano_referencia', $ano)
            ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
            ->get()
            ->sum(fn (FormularioFederacao $form) => (int) data_get($form->perfil, 'ativos', 0));
    }

    private function sociosAtivosFederacao(string $federacaoId, int $ano): int
    {
        $formulario = FormularioFederacao::where('federacao_id', $federacaoId)
            ->where('ano_referencia', $ano)
            ->where('status', EstatisticaService::FORMULARIO_ENTREGUE)
            ->first();

        return (int) data_get($formulario?->perfil, 'ativos', 0);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, FormularioLocal>  $entregues
     */
    private function dataLimite80Percent($entregues, int $totalFilhos): ?Carbon
    {
        if ($totalFilhos <= 0) {
            return null;
        }

        $minimo = (int) ceil($totalFilhos * 0.8);
        $ordenadas = $entregues
            ->map(function (FormularioLocal $form) {
                $data = $form->enviado_em ?? $form->updated_at ?? $form->created_at;

                return $data instanceof Carbon ? $data : ($data ? Carbon::parse($data) : null);
            })
            ->filter()
            ->sort()
            ->values();

        if ($ordenadas->count() < $minimo) {
            return null;
        }

        return $ordenadas->get($minimo - 1);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, FormularioLocal>  $entregues
     */
    private function dataMaioriaEntrega($entregues, int $totalFilhos): ?Carbon
    {
        return $this->dataLimite80Percent($entregues, $totalFilhos);
    }

    /** @param  \Illuminate\Support\Collection<int, string>|array<int, string>  $localIds */
    private function contarProgramacoes($localIds, int $ano): array
    {
        $ids = collect($localIds);
        $total = $ids->count();
        if ($total === 0) {
            return ['total' => 0, 'com_programacao' => 0, 'percentual' => 0.0];
        }

        $comProgramacao = FormularioLocal::whereIn('local_id', $ids)
            ->where('ano_referencia', $ano)
            ->get()
            ->filter(function (FormularioLocal $form) {
                $social = (int) data_get($form->programacoes, 'social', 0);
                $evangelistico = (int) data_get($form->programacoes, 'evangelistico', 0);

                return ($social + $evangelistico) > 0;
            })
            ->count();

        return [
            'total' => $total,
            'com_programacao' => $comProgramacao,
            'percentual' => ($comProgramacao * 100) / $total,
        ];
    }

    private function extrairAci(?array $aci, string $campo): float
    {
        if (! is_array($aci)) {
            return 0;
        }
        $raw = $aci[$campo] ?? 0;
        if (is_numeric($raw)) {
            return (float) $raw;
        }
        if (is_string($raw) && $raw !== '') {
            return FormHelper::converterParaFloat($raw);
        }

        return 0;
    }

    private function valorPrevistoAci(int $sociosAtivos, bool $sinodal, int $ano): float
    {
        $fator = $sinodal ? 0.25 : 0.5;

        return $sociosAtivos * ValorAciAno::valorPara($ano) * $fator;
    }

    /** @return array{conexao: bool, eventos: array<string, int>} */
    private function conquistas(
        NaturezaInstancia $natureza,
        ?string $sinodalId,
        ?string $federacaoId,
        int $ano
    ): array {
        $query = Conquista::withoutGlobalScopes()
            ->where('ano_referencia', $ano)
            ->where('ciclo', $this->calendario->ciclo());

        if ($natureza === NaturezaInstancia::Sinodal) {
            $query->where('sinodal_id', $sinodalId);
        } else {
            $query->where('federacao_id', $federacaoId);
        }

        $itens = $query->get();

        return [
            'conexao' => $itens->contains(fn (Conquista $c) => $c->tipo === TipoConquista::ConexaoMissionaria),
            'eventos' => $this->pontuadorEventos->agregarPorTipo($itens),
        ];
    }
}
