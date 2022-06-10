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
use Exception;
use Illuminate\Http\Request;
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
                'estrutura' => $request->federacao_ump,
                'perfil' => $request->perfil,
                'estado_civil' => $request->estado_civil,
                'escolaridade' => $request->escolaridade,
                'deficiencias' => $request->deficiencia,
                'programacoes_federacoes' => $request->programacoes_federacoes,
                'programacoes_locais' => $request->programacoes_locais,
                'programacoes_sinodal' => $request->programacoes,
                'aci' => $request->aci,
                'ano_referencia' => date('Y'),
                'sinodal_id' => $request->sinodal_id
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
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
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Salvar");           
        }
    }

    public static function delete(FormularioSinodal $formulario)
    {
        try {
            $formulario->delete();
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
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
            dd($th->getMessage(), $th->getFile(), $th->getLine());
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

    

    public static function validarImportacao(Request $request)
    {
        try {
            $classe_importacao = new FormularioSinodalImport();
            Excel::import($classe_importacao, request()->file('planilha'));
            $collection = collect($classe_importacao->data)->where('ano_referencia', date('Y'))->map(function($item) {
                return [
                    'id_planilha' => $item['info_federacao']['id'],
                    'presbiterio' => $item['info_federacao']['presbiterio']
                ];
            });
            return $collection;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw new Exception("Erro durante importação", 1);
            
        }
    }

    public static function importar(Request $request)
    {
        try {
            $classe_importacao = new FormularioSinodalImport();
            Excel::import($classe_importacao, request()->file('planilha'));
            DB::beginTransaction();
            foreach ($classe_importacao->data as $resposta) {
                self::storeFormularioFederacao($resposta);
            }
            DB::commit();

            self::storeV2($request);


        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public static function storeFormularioFederacao(array $dados)
    {
        try {
            
            FormularioFederacao::updateOrCreate(
                [
                    'ano_referencia' => $dados['ano_referencia'],
                    'federacao_id' => $dados['federacao_id']
                ],
                [
                    'perfil' => $dados['perfil'],
                    'estado_civil' => $dados['estado_civil'],
                    'escolaridade' => $dados['escolaridade'],
                    'deficiencias' => $dados['deficiencias'],
                    'programacoes_locais' => $dados['programacoes_locais'],
                    'programacoes' => $dados['programacoes'],
                    'aci' => $dados['aci'],
                    'ano_referencia' => $dados['ano_referencia'],
                    'federacao_id' => $dados['federacao_id'],
                    'estrutura' => $dados['estrutura']
                ]
            );
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Salvar");           
        }
    }

    public static function getFederacoes()
    {
        try {
            $federacoes = Auth::user()->sinodais->pluck('id');
            return Federacao::whereIn('sinodal_id', $federacoes)->get()->map(function($federacao) {
                return ['id' => $federacao->id, 'text' => $federacao->sigla];
            });
        } catch (\Throwable $th) {
            throw new Exception("Error durante a busca de estados", 1);            
        }
    }
}