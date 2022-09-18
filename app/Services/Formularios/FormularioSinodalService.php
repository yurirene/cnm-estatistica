<?php

namespace App\Services\Formularios;

use App\Imports\FormularioSinodalImport;
use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioSinodal;
use App\Models\FormularioLocal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Services\Formularios\Totalizadores\TotalizadorFormularioSinodalService;
use App\Services\LogErroService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class FormularioSinodalService
{

    public static function store(Request $request)
    {
        try {
            FormularioSinodal::updateOrCreate(
                [
                    'ano_referencia' => date('Y'),
                    'sinodal_id' => $request->sinodal_id
                ],
                [
                'estrutura' => $request->estrutura,
                'perfil' => $request->perfil,
                'estado_civil' => $request->estado_civil,
                'escolaridade' => $request->escolaridade,
                'deficiencias' => $request->deficiencias,
                'programacoes_federacoes' => $request->programacoes_federacoes,
                'programacoes_locais' => $request->programacoes_locais,
                'programacoes' => $request->programacoes,
                'aci' => $request->aci,
                'ano_referencia' => date('Y'),
                'sinodal_id' => $request->sinodal_id
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw new Exception("Erro ao Salvar");           
        }
    }

    public static function storeV2(Request $request)
    {
        try {
            $totalizador = TotalizadorFormularioSinodalService::totalizador($request->sinodal_id);
            FormularioSinodal::updateOrCreate(
                [
                    'ano_referencia' => date('Y'),
                    'sinodal_id' => $request->sinodal_id
                ],
                [
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'estrutura' => $totalizador['estrutura'],
                'programacoes_federacoes' => $totalizador['programacoes_federacao'],
                'programacoes_locais' => $totalizador['programacoes_locais'],
                'programacoes' => $request->programacoes,
                'aci' => $totalizador['programacoes_locais'] + ['repasse' => $request->aci],
                'ano_referencia' => date('Y'),
                'sinodal_id' => $request->sinodal_id
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw new Exception("Erro ao Salvar");           
        }
    }

    public static function delete(FormularioSinodal $formulario)
    {
        try {
            $formulario->delete();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw new Exception("Erro ao Atualizar");
            
        }
    }

    public static function verificarColeta()
    {
        try {
            $parametro_ativo = Parametro::where('nome', 'coleta_dados')->first()->valor == 'SIM';
            return $parametro_ativo;
        } catch (\Throwable $th) {
            throw new Exception("Erro ao Verificar Coleta");
        }
    }

    public static function getFormularioAnoCorrente()
    {
        return FormularioSinodal::where('sinodal_id', Auth::user()->sinodais->first()->id)
            ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
            ->first();
    }

    public static function showFormulario($id)
    {
        try {
            $formulario = FormularioSinodal::find($id);
            $resumo = GraficoFormularioService::formatarResumo($formulario);
            $grafico = GraficoFormularioService::formatarGrafico($formulario);
            return [
                'resumo' => $resumo,
                'grafico' => $grafico
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function getAnosFormulariosRespondidos()
    {
        try {
            return FormularioSinodal::whereIn('sinodal_id', Auth::user()->sinodais->pluck('id'))
                ->get()
                ->pluck('ano_referencia', 'id');
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }

}