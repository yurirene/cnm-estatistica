<?php

namespace App\Services\Formularios;

use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioSinodal;
use App\Models\FormularioLocal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Sinodal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FormularioSinodalService
{

    public static function store(Request $request)
    {
        try {
            FormularioSinodal::create([
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
            $totalizador = self::totalizador($request->federacao_id);
            FormularioSinodal::create([
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'programacoes_federacoes' => $totalizador['programacoes_federacao'],
                'programacoes_locais' => $totalizador['programacoes_locais'],
                'programacoes' => $request->programacoes,
                'aci' => $request->aci,
                'ano_referencia' => date('Y'),
                'federacao_id' => $request->federacao_id
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
            $existe_formulario = FormularioSinodal::where('sinodal_id', Auth::user()->sinodais->first()->id)
                ->where('ano_referencia', date('Y'))
                ->get()
                ->isEmpty();
            return $existe_formulario && $parametro_ativo;
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

    public static function totalizador($id)
    {
        $federacoes = Federacao::where('federacao_id', $id)->get()->pluck('id');
        try {
            $formularios = FormularioFederacao::whereIn('federacao_id', $federacoes)->where('ano_referencia', date('Y'))->get();
            
            $totalizador = [
                'aci' => 0,
                'perfil' => [
                    'ativos' => 0,
                    'cooperadores' => 0,
                    'homens' => 0,
                    'mulheres' => 0,
                    'menor19' => 0,
                    'de19a23' => 0,
                    'de24a29' => 0,
                    'de30a35' => 0
                ],
                'escolaridade' => [
                    'fundamental' => 0,
                    'medio' => 0,
                    'tecnico' => 0,
                    'superior' => 0,
                    'pos' => 0,
                    'desempregado' => 0,
                ],
                'estado_civil' => [
                    'solteiros' => 0,
                    'casados' => 0,
                    'divorciados' => 0,
                    'viuvos' => 0,
                    'filhos' => 0,
                ],
                'deficiencias' => [
                    'surdos' => 0,
                    'auditiva' => 0,
                    'cegos' => 0,
                    'baixa_visao' => 0,
                    'fisica_inferior' => 0,
                    'fisica_superior' => 0,
                    'neurologico' => 0,
                    'intelectual' => 0,
                ],
                'programacoes_federacao' => [
                    'social' => 0,
                    'oracao' => 0,
                    'evangelistica' => 0,
                    'espiritual' => 0,
                    'recreativo' => 0,
                ],
                'programacoes_locais' => [
                    'social' => 0,
                    'oracao' => 0,
                    'evangelistica' => 0,
                    'espiritual' => 0,
                    'recreativo' => 0,
                ]
            ];

            foreach ($formularios as $formulario) {                
                    $totalizador['perfil']['ativos'] += (isset($formulario->perfil['ativos']) ? intval($formulario->perfil['ativos']) : 0);
                    $totalizador['perfil']['cooperadores'] += (isset($formulario->perfil['cooperadores']) ? intval($formulario->perfil['cooperadores']) : 0);
                    $totalizador['perfil']['homens'] += (isset($formulario->perfil['homens']) ? intval($formulario->perfil['homens']) : 0);
                    $totalizador['perfil']['mulheres'] += (isset($formulario->perfil['mulheres']) ? intval($formulario->perfil['mulheres']) : 0);
                    $totalizador['perfil']['menor19'] += (isset($formulario->perfil['menor19']) ? intval($formulario->perfil['menor19']) : 0);
                    $totalizador['perfil']['de19a23'] += (isset($formulario->perfil['de19a23']) ? intval($formulario->perfil['de19a23']) : 0);
                    $totalizador['perfil']['de24a29'] += (isset($formulario->perfil['de24a29']) ? intval($formulario->perfil['de24a29']) : 0);
                    $totalizador['perfil']['de30a35'] += (isset($formulario->perfil['de30a35']) ? intval($formulario->perfil['de30a35']) : 0);
                    $totalizador['escolaridade']['fundamental'] += (isset($formulario->escolaridade['fundamental']) ? intval($formulario->escolaridade['fundamental']) : 0);
                    $totalizador['escolaridade']['medio'] += (isset($formulario->escolaridade['medio']) ? intval($formulario->escolaridade['medio']) : 0);
                    $totalizador['escolaridade']['tecnico'] += (isset($formulario->escolaridade['tecnico']) ? intval($formulario->escolaridade['tecnico']) : 0);
                    $totalizador['escolaridade']['superior'] += (isset($formulario->escolaridade['superior']) ? intval($formulario->escolaridade['superior']) : 0);
                    $totalizador['escolaridade']['pos'] += (isset($formulario->escolaridade['pos']) ? intval($formulario->escolaridade['pos']) : 0);
                    $totalizador['escolaridade']['desempregado'] += (isset($formulario->escolaridade['desempregado']) ? intval($formulario->escolaridade['desempregado']) : 0);
                    $totalizador['estado_civil']['solteiros'] += (isset($formulario->estado_civil['solteiros']) ? intval($formulario->estado_civil['solteiros']) : 0);
                    $totalizador['estado_civil']['casados'] += (isset($formulario->estado_civil['casados']) ? intval($formulario->estado_civil['casados']) : 0);
                    $totalizador['estado_civil']['divorciados'] += (isset($formulario->estado_civil['divorciados']) ? intval($formulario->estado_civil['divorciados']) : 0);
                    $totalizador['estado_civil']['viuvos'] += (isset($formulario->estado_civil['viuvos']) ? intval($formulario->estado_civil['viuvos']) : 0);
                    $totalizador['estado_civil']['filhos'] += (isset($formulario->estado_civil['filhos']) ? intval($formulario->estado_civil['filhos']) : 0);
                    $totalizador['deficiencias']['surdos'] += (isset($formulario->deficiencias['surdos']) ? intval($formulario->deficiencias['surdos']) : 0);
                    $totalizador['deficiencias']['auditiva'] += (isset($formulario->deficiencias['auditiva']) ? intval($formulario->deficiencias['auditiva']) : 0);
                    $totalizador['deficiencias']['cegos'] += (isset($formulario->deficiencias['cegos']) ? intval($formulario->deficiencias['cegos']) : 0);
                    $totalizador['deficiencias']['baixa_visao'] += (isset($formulario->deficiencias['baixa_visao']) ? intval($formulario->deficiencias['baixa_visao']) : 0);
                    $totalizador['deficiencias']['fisica_inferior'] += (isset($formulario->deficiencias['fisica_inferior']) ? intval($formulario->deficiencias['fisica_inferior']) : 0);
                    $totalizador['deficiencias']['fisica_superior'] += (isset($formulario->deficiencias['fisica_superior']) ? intval($formulario->deficiencias['fisica_superior']) : 0);
                    $totalizador['deficiencias']['neurologico'] += (isset($formulario->deficiencias['neurologico']) ? intval($formulario->deficiencias['neurologico']) : 0);
                    $totalizador['deficiencias']['intelectual'] += (isset($formulario->deficiencias['intelectual']) ? intval($formulario->deficiencias['intelectual']) : 0);

                    
                    $totalizador['programacoes_federacao']['social'] += (isset($formulario->programacoes_federacao['social']) ? intval($formulario->programacoes_federacao['social']) : 0);
                    $totalizador['programacoes_federacao']['oracao'] += (isset($formulario->programacoes_federacao['oracao']) ? intval($formulario->programacoes_federacao['oracao']) : 0);
                    $totalizador['programacoes_federacao']['evangelistica'] += (isset($formulario->programacoes_federacao['evangelistica']) ? intval($formulario->programacoes_federacao['evangelistica']) : 0);
                    $totalizador['programacoes_federacao']['espiritual'] += (isset($formulario->programacoes_federacao['espiritual']) ? intval($formulario->programacoes_federacao['espiritual']) : 0);
                    $totalizador['programacoes_federacao']['recreativo'] += (isset($formulario->programacoes_federacao['recreativo']) ? intval($formulario->programacoes_federacao['recreativo']) : 0);
                    
                    
                    $totalizador['programacoes_locais']['social'] += (isset($formulario->programacoes_locais['social']) ? intval($formulario->programacoes_locais['social']) : 0);
                    $totalizador['programacoes_locais']['oracao'] += (isset($formulario->programacoes_locais['oracao']) ? intval($formulario->programacoes_locais['oracao']) : 0);
                    $totalizador['programacoes_locais']['evangelistica'] += (isset($formulario->programacoes_locais['evangelistica']) ? intval($formulario->programacoes_locais['evangelistica']) : 0);
                    $totalizador['programacoes_locais']['espiritual'] += (isset($formulario->programacoes_locais['espiritual']) ? intval($formulario->programacoes_locais['espiritual']) : 0);
                    $totalizador['programacoes_locais']['recreativo'] += (isset($formulario->programacoes_locais['recreativo']) ? intval($formulario->programacoes_locais['recreativo']) : 0);
            }
            return $totalizador;
        } catch (\Throwable $th) {
            throw new Exception("Erro no Totalizador", 1);
            
        }
    }

}