<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Enums\IntencaoPerguntaEnum;
use App\Services\EstatisticaInteligente\Intencao\IntencaoClassifier;
use Tests\TestCase;

class IntencaoClassifierTest extends TestCase
{
    private IntencaoClassifier $classifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classifier = new IntencaoClassifier();
    }

    public function test_historico(): void
    {
        $this->assertSame(
            IntencaoPerguntaEnum::Historico,
            $this->classifier->classificar('Como evoluímos nos últimos quatro anos?')
        );
    }

    public function test_comparativo(): void
    {
        $this->assertSame(
            IntencaoPerguntaEnum::Comparativo,
            $this->classifier->classificar('Como estamos comparado com outras UMPs da mediana?')
        );
    }

    public function test_crescimento(): void
    {
        $this->assertSame(
            IntencaoPerguntaEnum::Crescimento,
            $this->classifier->classificar('O número de ativos cresceu neste ano?')
        );
    }

    public function test_atividades(): void
    {
        $this->assertSame(
            IntencaoPerguntaEnum::Atividades,
            $this->classifier->classificar('Quantas atividades foram registradas?')
        );
    }

    public function test_desconhecida(): void
    {
        $this->assertSame(
            IntencaoPerguntaEnum::Desconhecida,
            $this->classifier->classificar('Qual o melhor lanche para o retiro?')
        );
    }
}
