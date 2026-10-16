<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Services\EstatisticaInteligente\Contratos\AnaliseIaInterface;
use App\Services\EstatisticaInteligente\TemplateAnaliseService;
use Tests\TestCase;

class AnaliseIaProviderTest extends TestCase
{
    public function test_container_resolve_template_como_padrao(): void
    {
        $this->assertInstanceOf(
            TemplateAnaliseService::class,
            $this->app->make(AnaliseIaInterface::class)
        );
    }
}
