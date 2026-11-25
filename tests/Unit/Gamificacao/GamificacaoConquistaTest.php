<?php

namespace Tests\Unit\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Models\Gamificacao\Conquista;
use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\GamificacaoAtualizacaoService;
use App\Services\Gamificacao\GamificacaoConquistaService;
use App\Services\Gamificacao\Guards\ConquistaGuard;
use App\Services\Gamificacao\Guards\EscritorPlacar;
use App\Services\Gamificacao\Regras\CalendarioGamificacao;
use Tests\TestCase;

class GamificacaoConquistaTest extends TestCase
{
    public function test_nao_grava_conquista_fora_do_escritor(): void
    {
        $this->expectException(GamificacaoException::class);
        $this->expectExceptionMessage('Conquistas só podem ser gravadas pelo serviço de atualização.');

        $conquista = new Conquista();
        $conquista->tipo = TipoConquista::Djp;
        $conquista->save();
    }

    public function test_guard_rejeita_natureza_cruzada(): void
    {
        $this->expectException(GamificacaoException::class);
        (new ConquistaGuard())->validar(
            TipoConquista::ConexaoMissionaria,
            NaturezaInstancia::Federacao,
            null,
            'fed-1',
            2026,
            '2026-2030'
        );
    }

    public function test_guard_exige_federacao_em_evento(): void
    {
        $this->expectException(GamificacaoException::class);
        (new ConquistaGuard())->validar(
            TipoConquista::Djp,
            NaturezaInstancia::Federacao,
            null,
            null,
            2026,
            '2026-2030'
        );
    }

    public function test_servico_recusa_conexao_com_federacao(): void
    {
        $this->expectException(GamificacaoException::class);
        $this->expectExceptionMessage('Conexão Missionária é lançada para sinodais, não para federações.');

        $this->servico()->conceder(
            TipoConquista::ConexaoMissionaria,
            ['fed-1'],
            [],
            'Conexão Missionária',
            2026
        );
    }

    public function test_servico_recusa_evento_com_sinodal(): void
    {
        $this->expectException(GamificacaoException::class);
        $this->expectExceptionMessage('Este evento é lançado para federações, não para sinodais.');

        $this->servico()->conceder(
            TipoConquista::PmfOficial,
            [],
            ['sin-1'],
            'PMF 2026',
            2026
        );
    }

    public function test_servico_recusa_lista_vazia(): void
    {
        $this->expectException(GamificacaoException::class);
        $this->expectExceptionMessage('Selecione ao menos uma instância.');

        $this->servico()->conceder(
            TipoConquista::Djp,
            [],
            [],
            'DJP',
            2026
        );
    }

    public function test_servico_recusa_selo_manual(): void
    {
        $this->expectException(GamificacaoException::class);
        $this->expectExceptionMessage('Selos são concedidos automaticamente');

        $this->servico()->conceder(
            TipoConquista::SeloEngajamento100,
            [],
            ['sin-1'],
            'Selo',
            2026
        );
    }

    public function test_esporadico_exige_nome(): void
    {
        $this->expectException(GamificacaoException::class);
        $this->expectExceptionMessage('Informe o nome do evento esporádico.');

        $this->servico()->conceder(
            TipoConquista::Esporadico,
            ['fed-1'],
            [],
            '   ',
            2026
        );
    }

    public function test_eventos_sao_unicos_no_ano_exceto_esporadico(): void
    {
        $this->assertTrue(TipoConquista::PmfOficial->unicoNoAno());
        $this->assertTrue(TipoConquista::PmfParceria->unicoNoAno());
        $this->assertTrue(TipoConquista::Djp->unicoNoAno());
        $this->assertTrue(TipoConquista::ConexaoMissionaria->unicoNoAno());
        $this->assertTrue(TipoConquista::SeloEntregaAntecipada->ehSelo());
        $this->assertFalse(TipoConquista::Esporadico->unicoNoAno());
        $this->assertFalse(TipoConquista::SeloEngajamento100->ehLancavelManual());
    }

    public function test_escritor_libera_gravacao(): void
    {
        $this->assertFalse(EscritorPlacar::liberado());
        EscritorPlacar::executar(function () {
            $this->assertTrue(EscritorPlacar::liberado());
        });
        $this->assertFalse(EscritorPlacar::liberado());
    }

    private function servico(): GamificacaoConquistaService
    {
        return new GamificacaoConquistaService(
            new ConquistaGuard(),
            new CalendarioGamificacao(),
            $this->createMock(GamificacaoAtualizacaoService::class),
        );
    }
}
