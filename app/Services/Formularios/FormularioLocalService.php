<?php

namespace App\Services\Formularios;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioLocal;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
use App\Services\LogErroService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormularioLocalService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $perfil = array_map(function($item) {
                return intval($item);
            },$request->perfil);
            $estado_civil = array_map(function($item) {
                return intval($item);
            },$request->estado_civil);
            $deficiencias = collect($request->deficiencias)->map(function($item,$key) {
                if ($key == 'outras') {
                    return $item;
                }
                return intval($item);
            })->toArray();
            $escolaridade = array_map(function($item) {
                return intval($item);
            },$request->escolaridade);

            $programacoes = array_map(function($item) {
                return intval($item);
            }, $request->programacoes);

            $anoReferencia = EstatisticaService::getAnoReferencia();
            $formulario = FormularioLocal::updateOrCreate(
                [
                    'ano_referencia' => $anoReferencia,
                    'local_id' => $request->local_id
                ],
                [
                    'perfil' => $perfil,
                    'estado_civil' => $estado_civil,
                    'escolaridade' => $escolaridade,
                    'deficiencias' => $deficiencias,
                    'programacoes' => $programacoes,
                    'aci' => $request->aci,
                    'ano_referencia' => $anoReferencia,
                    'local_id' => $request->local_id
                ]
            );
            EstatisticaService::atualizarRelatorioGeral();
            AtualizarAutomaticamenteFormulariosService::atualizarFederacao($formulario);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Salvar");
        }
    }

    public static function delete(FormularioLocal $formulario)
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
            return Parametro::where('nome', 'coleta_dados')->first()->valor == 'SIM';
        } catch (\Throwable $th) {
            throw new Exception("Erro ao Verificar Coleta");
        }
    }

    public static function showFormulario($id)
    {
        try {
            $formulario = FormularioLocal::find($id);
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
            return FormularioLocal::whereIn('local_id', Auth::user()->locais->pluck('id'))
                ->get()
                ->pluck('ano_referencia', 'id');
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 1);
        }
    }

    public static function getFormularioAnoCorrente()
    {
        return FormularioLocal::where('local_id', Auth::user()->locais->first()->id)
            ->where('ano_referencia', EstatisticaService::getAnoReferencia())
            ->first();
    }

    public static function getFormulario($ano)
    {
        return FormularioLocal::where('local_id', Auth::user()->locais->first()->id)
            ->where('ano_referencia', $ano)
            ->first();
    }

    public static function getFormularioLocal($local)
    {
        return FormularioLocal::where('local_id', $local)
            ->where('ano_referencia', EstatisticaService::getAnoReferencia())
            ->first();
    }
}
