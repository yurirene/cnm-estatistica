<?php

namespace Tests\Feature;

use App\Models\FormularioComplementarFederacao;
use App\Models\FormularioComplementarSinodal;
use App\Services\Formularios\FormularioComplementarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;


class FormularioComplementarServiceTest extends TestCase
{
    public function test_get_formulario_complementar_federacao_exists()
    {
        $instanciaId = '1';
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_FEDERACAO;

        $formulario = FormularioComplementarFederacao::create([
            'federacao_id' => $instanciaId
        ]);

        $result = FormularioComplementarService::getFormularioComplementar($instanciaId, $tipo);

        $this->assertInstanceOf(FormularioComplementarFederacao::class, $result);
        $this->assertEquals($formulario->id, $result->id);
    }

    public function test_get_formulario_complementar_federacao_not_exists()
    {
        $instanciaId = '1';
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_FEDERACAO;

        $result = FormularioComplementarService::getFormularioComplementar($instanciaId, $tipo);

        $this->assertInstanceOf(FormularioComplementarFederacao::class, $result);
        $this->assertEquals($instanciaId, $result->federacao_id);
    }

    public function test_get_formulario_complementar_sinodal_exists()
    {
        $instanciaId = '1';
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_SINODAL;

        $formulario = FormularioComplementarSinodal::create([
            'sinodal_id' => $instanciaId
        ]);

        $result = FormularioComplementarService::getFormularioComplementar($instanciaId, $tipo);

        $this->assertInstanceOf(FormularioComplementarSinodal::class, $result);
        $this->assertEquals($formulario->id, $result->id);
    }

    public function test_get_formulario_complementar_sinodal_not_exists()
    {
        $instanciaId = '1';
        $tipo = FormularioComplementarService::TIPO_FORMULARIO_SINODAL;

        $result = FormularioComplementarService::getFormularioComplementar($instanciaId, $tipo);

        $this->assertInstanceOf(FormularioComplementarSinodal::class, $result);
        $this->assertEquals($instanciaId, $result->sinodal_id);
    }
}