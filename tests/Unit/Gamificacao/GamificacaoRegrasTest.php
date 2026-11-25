<?php

namespace Tests\Unit\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Services\Gamificacao\Calculadores\AciCalculador;
use App\Services\Gamificacao\Calculadores\CalculadorFactory;
use App\Services\Gamificacao\Calculadores\ConexaoMissionariaCalculador;
use App\Services\Gamificacao\Calculadores\EntregaAntecipadaCalculador;
use App\Services\Gamificacao\Calculadores\EstatisticaCalculador;
use App\Services\Gamificacao\Calculadores\EvangelismoCalculador;
use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\Enums\Liga;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use App\Services\Gamificacao\Regras\DescontoResolver;
use App\Services\Gamificacao\Regras\LigaResolver;
use App\Services\Gamificacao\Regras\ValoresLegaisPilar;
use Carbon\Carbon;
use Tests\TestCase;

class GamificacaoRegrasTest extends TestCase
{
    private function contexto(array $over = []): ContextoInstancia
    {
        $base = [
            'natureza' => NaturezaInstancia::Federacao,
            'sinodalId' => null,
            'federacaoId' => 'fed-1',
            'ano' => 2027,
            'ciclo' => '2026-2030',
            'sociosAtivos' => 50,
            'sociosParaLiga' => 50,
            'totalFilhos' => 10,
            'filhosEntregaram' => 8,
            'percentualEstatistica' => 80,
            'aciComprovanteAnexado' => true,
            'aciRepasseInformado' => true,
            'aciValorRepassado' => 100,
            'aciValorPrevisto' => 100,
            'umpsTotal' => 10,
            'umpsComProgramacao' => 6,
            'percentualEvangelismo' => 60,
            'ceConfirmada' => false,
            'temConexaoMissionaria' => false,
            'dataEntregaEstatistica' => Carbon::parse('2028-01-09'),
            'pontosEventosPorTipo' => [],
        ];

        $dados = array_merge($base, $over);

        return new ContextoInstancia(...$dados);
    }

    public function test_liga_resolver_faixas_sinodal(): void
    {
        $resolver = new LigaResolver();

        $this->assertSame(Liga::Simonton, $resolver->resolver(NaturezaInstancia::Sinodal, 300));
        $this->assertSame(Liga::Blackford, $resolver->resolver(NaturezaInstancia::Sinodal, 150));
        $this->assertSame(Liga::Conceicao, $resolver->resolver(NaturezaInstancia::Sinodal, 149));
    }

    public function test_desconto_resolver_sinodal(): void
    {
        $resolver = new DescontoResolver();

        $this->assertSame(0, $resolver->projetar(NaturezaInstancia::Sinodal, 208));
        $this->assertSame(50, $resolver->projetar(NaturezaInstancia::Sinodal, 209));
        $this->assertSame(75, $resolver->projetar(NaturezaInstancia::Sinodal, 266));
        $this->assertSame(100, $resolver->projetar(NaturezaInstancia::Sinodal, 323));
        $this->assertSame(1, $resolver->pontosParaProximo(NaturezaInstancia::Sinodal, 208));
    }

    public function test_valores_legais_rejeita_parcial_estatistica(): void
    {
        $this->expectException(GamificacaoException::class);
        (new ValoresLegaisPilar())->validar(Pilar::Estatistica, 10);
    }

    public function test_estatistica_tudo_ou_nada(): void
    {
        $calc = new EstatisticaCalculador(new CalendarioGamificacao());
        $ok = $calc->calcular($this->contexto(['percentualEstatistica' => 80]));
        $no = $calc->calcular($this->contexto(['percentualEstatistica' => 79]));

        $this->assertSame(25, $ok->pontos);
        $this->assertSame(0, $no->pontos);
        $this->assertSame(StatusPilar::Risco, $no->status);
    }

    public function test_aci_exige_oitenta_por_cento(): void
    {
        $calc = new AciCalculador(new CalendarioGamificacao());
        $ok = $calc->calcular($this->contexto([
            'aciValorRepassado' => 80,
            'aciValorPrevisto' => 100,
        ]));
        $no = $calc->calcular($this->contexto([
            'aciValorRepassado' => 79,
            'aciValorPrevisto' => 100,
        ]));

        $this->assertSame(25, $ok->pontos);
        $this->assertSame(0, $no->pontos);
    }

    public function test_aci_sinodal_exige_meta_atingida_da_tesouraria(): void
    {
        $calc = new AciCalculador(new CalendarioGamificacao());
        $ok = $calc->calcular($this->contexto([
            'natureza' => NaturezaInstancia::Sinodal,
            'sinodalId' => 'sin-1',
            'federacaoId' => null,
            'aciMetaAtingida' => true,
            'aciValorRepassado' => 10,
            'aciValorPrevisto' => 100,
        ]));
        $no = $calc->calcular($this->contexto([
            'natureza' => NaturezaInstancia::Sinodal,
            'sinodalId' => 'sin-1',
            'federacaoId' => null,
            'aciComprovanteAnexado' => true,
            'aciMetaAtingida' => false,
            'aciValorRepassado' => 100,
            'aciValorPrevisto' => 100,
        ]));

        $this->assertSame(25, $ok->pontos);
        $this->assertSame(0, $no->pontos);
    }

    public function test_evangelismo_faixas(): void
    {
        $calc = new EvangelismoCalculador();
        $this->assertSame(15, $calc->calcular($this->contexto(['percentualEvangelismo' => 60]))->pontos);
        $this->assertSame(25, $calc->calcular($this->contexto(['percentualEvangelismo' => 80]))->pontos);
        $this->assertSame(30, $calc->calcular($this->contexto(['percentualEvangelismo' => 100]))->pontos);
        $this->assertSame(0, $calc->calcular($this->contexto(['percentualEvangelismo' => 59]))->pontos);

        $resultado = $calc->calcular($this->contexto(['percentualEvangelismo' => 60]));
        $this->assertNull($resultado->ctaLabel);
        $this->assertNull($resultado->ctaRota);
        $this->assertNotEmpty($resultado->hint);
        $this->assertStringContainsString('60% das UMPs com programação registrada', $resultado->rodape);
        $this->assertStringNotContainsString('60%=15', $resultado->rodape);
    }

    public function test_entrega_antecipada_exige_estatistica_e_prazo(): void
    {
        $calc = new EntregaAntecipadaCalculador(new CalendarioGamificacao());
        $ok = $calc->calcular($this->contexto());
        $tarde = $calc->calcular($this->contexto([
            'dataEntregaEstatistica' => Carbon::parse('2028-01-11'),
        ]));
        $semBase = $calc->calcular($this->contexto(['percentualEstatistica' => 50]));

        $this->assertSame(10, $ok->pontos);
        $this->assertSame(0, $tarde->pontos);
        $this->assertSame(0, $semBase->pontos);
        $this->assertSame(StatusPilar::Indisponivel, $semBase->status);
    }

    public function test_conexao_missionaria_sem_conquista_fica_disponivel(): void
    {
        $calc = new ConexaoMissionariaCalculador();
        $resultado = $calc->calcular($this->contexto([
            'natureza' => NaturezaInstancia::Sinodal,
            'sinodalId' => 'sin-1',
            'federacaoId' => null,
        ]));

        $this->assertSame(0, $resultado->pontos);
        $this->assertSame(StatusPilar::Disponivel, $resultado->status);
    }

    public function test_eventos_aplicam_limites_por_tipo_e_somam_esporadicos(): void
    {
        $pontuador = new \App\Services\Gamificacao\Regras\PontuadorEventos();
        $conquistas = [
            (object) ['tipo' => TipoConquista::PmfOficial],
            (object) ['tipo' => TipoConquista::PmfOficial],
            (object) ['tipo' => TipoConquista::PmfParceria],
            (object) ['tipo' => TipoConquista::Djp],
            (object) ['tipo' => TipoConquista::Esporadico],
            (object) ['tipo' => TipoConquista::Esporadico],
            (object) ['tipo' => TipoConquista::Esporadico],
            (object) ['tipo' => TipoConquista::ConexaoMissionaria],
        ];

        $porTipo = $pontuador->agregarPorTipo($conquistas);

        $this->assertSame(2, $porTipo[TipoConquista::PmfOficial->value]);
        $this->assertSame(2, $porTipo[TipoConquista::PmfParceria->value]);
        $this->assertSame(2, $porTipo[TipoConquista::Djp->value]);
        $this->assertSame(4, $porTipo[TipoConquista::Esporadico->value]);
        $this->assertArrayNotHasKey(TipoConquista::ConexaoMissionaria->value, $porTipo);
        $this->assertSame(10, $pontuador->total($porTipo, 10));
        $this->assertSame(8, $pontuador->total($porTipo, 8));
    }

    public function test_eventos_calculador_respeita_teto_do_pilar(): void
    {
        $calc = new \App\Services\Gamificacao\Calculadores\EventosCalculador();
        $completo = $calc->calcular($this->contexto([
            'pontosEventosPorTipo' => [
                TipoConquista::PmfOficial->value => 2,
                TipoConquista::PmfParceria->value => 2,
                TipoConquista::Djp->value => 2,
                TipoConquista::Esporadico->value => 4,
            ],
        ]));
        $duplicado = $calc->calcular($this->contexto([
            'pontosEventosPorTipo' => [
                TipoConquista::PmfOficial->value => 10,
                TipoConquista::Esporadico->value => 2,
            ],
        ]));

        $this->assertSame(10, $completo->pontos);
        $this->assertSame(Pilar::Eventos->maximo(), $completo->pontosMaximo);
        $this->assertSame(4, $duplicado->pontos);
    }

    public function test_ano_inicial_so_estatistica_e_aci(): void
    {
        $factory = app(CalculadorFactory::class);
        $pilares2026 = collect($factory->para(NaturezaInstancia::Federacao, 2026))
            ->map(fn ($c) => $c->calcular($this->contexto(['ano' => 2026]))->pilar)
            ->all();
        $pilares2027 = collect($factory->para(NaturezaInstancia::Federacao, 2027))
            ->map(fn ($c) => $c->calcular($this->contexto(['ano' => 2027]))->pilar)
            ->all();

        $this->assertSame([Pilar::Estatistica, Pilar::Aci], $pilares2026);
        $this->assertContains(Pilar::Evangelismo, $pilares2027);
        $this->assertContains(Pilar::EntregaAntecipada, $pilares2027);
        $this->assertSame(50, NaturezaInstancia::Federacao->tetoAnual(2026));
        $this->assertSame(100, NaturezaInstancia::Federacao->tetoAnual(2027));
    }
}
