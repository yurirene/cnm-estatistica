<?php

namespace Tests\Unit\EstatisticaInteligente;

use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Services\EstatisticaInteligente\AlvoAnaliseResolver;
use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;
use Tests\TestCase;

class AlvoAnaliseResolverTest extends TestCase
{
    public function test_apos_local_sobe_ate_nacional(): void
    {
        $local = new Local();
        $local->id = 'ump-1';
        $local->federacao_id = 'fed-1';
        $local->sinodal_id = 'sin-1';
        $local->regiao_id = 3;

        $alvos = (new AlvoAnaliseResolver())->aposLocal($local);
        $niveis = array_map(static fn (array $alvo) => $alvo['nivel']->value, $alvos);

        $this->assertSame([
            NivelEstatisticoEnum::Local->value,
            NivelEstatisticoEnum::Federacao->value,
            NivelEstatisticoEnum::Sinodal->value,
            NivelEstatisticoEnum::Regiao->value,
            NivelEstatisticoEnum::Nacional->value,
        ], $niveis);
        $this->assertSame('3', $alvos[3]['id']);
        $this->assertSame('', $alvos[4]['id']);
    }

    public function test_apos_federacao_nao_inclui_local(): void
    {
        $federacao = new Federacao();
        $federacao->id = 'fed-1';
        $federacao->sinodal_id = 'sin-1';
        $federacao->regiao_id = 2;

        $niveis = array_map(
            static fn (array $alvo) => $alvo['nivel']->value,
            (new AlvoAnaliseResolver())->aposFederacao($federacao)
        );

        $this->assertSame([
            NivelEstatisticoEnum::Federacao->value,
            NivelEstatisticoEnum::Sinodal->value,
            NivelEstatisticoEnum::Regiao->value,
            NivelEstatisticoEnum::Nacional->value,
        ], $niveis);
    }

    public function test_apos_sinodal(): void
    {
        $sinodal = new Sinodal();
        $sinodal->id = 'sin-1';
        $sinodal->regiao_id = 1;

        $niveis = array_map(
            static fn (array $alvo) => $alvo['nivel']->value,
            (new AlvoAnaliseResolver())->aposSinodal($sinodal)
        );

        $this->assertSame([
            NivelEstatisticoEnum::Sinodal->value,
            NivelEstatisticoEnum::Regiao->value,
            NivelEstatisticoEnum::Nacional->value,
        ], $niveis);
    }
}
