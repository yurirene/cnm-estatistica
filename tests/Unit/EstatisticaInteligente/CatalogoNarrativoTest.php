<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Catalogo\CatalogoNarrativo;
use App\Services\EstatisticaInteligente\Catalogo\TemplateRendererService;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;
use Tests\TestCase;

class CatalogoNarrativoTest extends TestCase
{
    private CatalogoNarrativo $catalogo;

    private TemplateRendererService $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->catalogo = CatalogoNarrativo::fromConfig();
        $this->renderer = new TemplateRendererService($this->catalogo);
    }

    public function test_todo_evento_tem_ao_menos_uma_variante(): void
    {
        foreach (EventoNarrativoEnum::cases() as $evento) {
            $this->assertNotEmpty(
                $this->catalogo->textos($evento),
                "Evento {$evento->value} sem variante no catálogo."
            );
        }
    }

    public function test_interpolacao_nao_deixa_placeholder(): void
    {
        $variaveis = [
            'percentual' => 20.0,
            'indicador_nome' => 'membros ativos',
            'ano_anterior' => 2025,
            'ano_atual' => 2026,
            'indice' => 62.5,
            'categorias' => 5,
            'percentil' => 80,
            'quantidade' => 1,
            'score' => 90,
            'grupo_pares' => 'das UMPs da mesma federação',
        ];

        foreach (EventoNarrativoEnum::cases() as $evento) {
            foreach ($this->catalogo->textos($evento) as $template) {
                $texto = $this->renderer->interpolar($template, $variaveis);
                $this->assertDoesNotMatchRegularExpression(
                    '/\{[a-z0-9_]+\}/i',
                    $texto,
                    "Placeholder residual em {$evento->value}: {$texto}"
                );
            }
        }
    }

    public function test_mesma_hash_e_mesmo_evento_selecionam_a_mesma_variante(): void
    {
        $evento = EventoNarrativoEnum::CRESCIMENTO_FORTE;
        $hash = hash('sha256', 'mesmo-contexto');

        $this->assertSame(
            $this->catalogo->selecionar($evento, $hash),
            $this->catalogo->selecionar($evento, $hash)
        );
    }

    public function test_hashes_diferentes_podem_variar_o_texto(): void
    {
        $evento = EventoNarrativoEnum::CRESCIMENTO_FORTE;
        $variantes = $this->catalogo->textos($evento);
        $encontradas = [];

        for ($i = 0; $i < 50; $i++) {
            $encontradas[] = $this->catalogo->selecionar($evento, hash('sha256', "ctx-{$i}"));
        }

        $this->assertNotEmpty(array_intersect($variantes, array_unique($encontradas)));
    }

    public function test_item_usa_titulo_e_severidade_do_evento(): void
    {
        $evento = EventoNarrativoDTO::de(EventoNarrativoEnum::CRESCIMENTO_FORTE, [
            'percentual' => 20,
            'indicador_nome' => 'membros ativos',
        ]);

        $item = $this->renderer->item($evento, hash('sha256', 'ctx'));

        $this->assertSame('Crescimento', $item->titulo);
        $this->assertSame('positivo', $item->tipo->value);
        $this->assertDoesNotMatchRegularExpression('/\{[a-z0-9_]+\}/i', $item->descricao);
    }
}
