<?php

namespace Tests\Feature;

use App\Models\FormularioComplementarFederacao;
use App\Models\FormularioComplementarSinodal;
use App\Services\Formularios\FormularioComplementarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesUsersAndRoles;
use Tests\TestCase;

class FormularioComplementarServiceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsersAndRoles;

    public function test_get_formulario_complementar_federacao_exists(): void
    {
        $sinodal = $this->createSinodal();
        $federacao = $this->createFederacao($sinodal);
        $ano = (int) date('Y');
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_FEDERACAO;

        $formulario = FormularioComplementarFederacao::create([
            'federacao_id' => $federacao->id,
            'ano' => $ano,
        ]);

        $result = FormularioComplementarService::getFormularioComplementar(
            $federacao->id,
            $tipo,
            $ano
        );

        $this->assertInstanceOf(FormularioComplementarFederacao::class, $result);
        $this->assertEquals($formulario->id, $result->id);
    }

    public function test_get_formulario_complementar_federacao_not_exists(): void
    {
        $sinodal = $this->createSinodal();
        $federacao = $this->createFederacao($sinodal);
        $ano = (int) date('Y');
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_FEDERACAO;

        $result = FormularioComplementarService::getFormularioComplementar(
            $federacao->id,
            $tipo,
            $ano
        );

        $this->assertInstanceOf(FormularioComplementarFederacao::class, $result);
        $this->assertEquals($federacao->id, $result->federacao_id);
        $this->assertEquals($ano, $result->ano);
    }

    public function test_get_formulario_complementar_sinodal_exists(): void
    {
        $sinodal = $this->createSinodal();
        $ano = (int) date('Y');
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_SINODAL;

        $formulario = FormularioComplementarSinodal::create([
            'sinodal_id' => $sinodal->id,
            'ano' => $ano,
        ]);

        $result = FormularioComplementarService::getFormularioComplementar(
            $sinodal->id,
            $tipo,
            $ano
        );

        $this->assertInstanceOf(FormularioComplementarSinodal::class, $result);
        $this->assertEquals($formulario->id, $result->id);
    }

    public function test_get_formulario_complementar_sinodal_not_exists(): void
    {
        $sinodal = $this->createSinodal();
        $ano = (int) date('Y');
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_SINODAL;

        $result = FormularioComplementarService::getFormularioComplementar(
            $sinodal->id,
            $tipo,
            $ano
        );

        $this->assertInstanceOf(FormularioComplementarSinodal::class, $result);
        $this->assertEquals($sinodal->id, $result->sinodal_id);
        $this->assertEquals($ano, $result->ano);
    }
}
