<?php

namespace App\Services\Formularios;

use App\Models\FormularioFederacao;
use App\Models\FormularioSinodal;
use App\Models\Parametro;
use App\Services\Formularios\Totalizadores\TotalizadorFormularioSinodalService;
use Illuminate\Database\Eloquent\Model;

class AtualizarAutomaticamenteFormulariosService
{

    public static function atualizarFederacao(Model $model) : void
    {
        try {
            $local = $model->local;
            $formulario_federacao = FormularioFederacao::where('federacao_id', $local->federacao_id)
                ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
                ->get();
            if ($formulario_federacao->isEmpty()) {
                return;
            }
            $totalizador = FormularioFederacaoService::totalizador($local->federacao_id);
            $formulario_federacao->first()->update([
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'programacoes_locais' => $totalizador['programacoes'],
            ]);
            self::atualizarSinodal($formulario_federacao->first());
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function atualizarSinodal(Model $model) : void
    {
        try {
            $federacao = $model->federacao;
            $formulario_sinodal = FormularioSinodal::where('sinodal_id', $federacao->sinodal_id)
                ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
                ->get();
            if ($formulario_sinodal->isEmpty()) {
                return;
            }
            $totalizador = TotalizadorFormularioSinodalService::totalizador($federacao->sinodal_id);
            $formulario_sinodal->first()->update([
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'programacoes_federacoes' => $totalizador['programacoes_federacao'],
                'programacoes_locais' => $totalizador['programacoes_locais'],
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
