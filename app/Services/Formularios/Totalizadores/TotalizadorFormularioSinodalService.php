<?php

namespace App\Services\Formularios\Totalizadores;

use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\Parametro;
use Exception;

class TotalizadorFormularioSinodalService
{
    public static function totalizador($id)
    {
        $federacoes = Federacao::where('sinodal_id', $id)->get()->pluck('id');
        try {
            $formularios = FormularioFederacao::whereIn('federacao_id', $federacoes)->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)->get();
            
            $totalizador = [
                'aci' => [
                    'ump_repassaram' => 0,
                    'ump_nao_repassaram' => 0,
                    'federacao_repassaram' => 0,
                    'federacao_nao_repassaram' => 0,
                ],
                'estrutura' => [
                    'ump_organizada' => 0,
                    'ump_nao_organizada' => 0,
                    'federacao_organizada' => 0,
                    'federacao_nao_organizada' => 0
                ],
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
                    $totalizador['aci']['ump_repassaram'] += (isset($formulario->aci['ump_repassaram']) ? intval($formulario->aci['ump_repassaram']) : 0);
                    $totalizador['aci']['ump_nao_repassaram'] += (isset($formulario->aci['ump_nao_repassaram']) ? intval($formulario->aci['ump_nao_repassaram']) : 0);

                    if ($formulario->aci['repasse'] == 'S' || $formulario->aci['repasse'] == 'SIM') {
                        $totalizador['aci']['federacao_repassaram'] ++;
                    } else {
                        $totalizador['aci']['federacao_nao_repassaram'] ++;
                    }

                    $totalizador['estrutura']['ump_organizada'] += (isset($formulario->estrutura['ump_organizada']) ? intval($formulario->estrutura['ump_organizada']) : 0);
                    $totalizador['estrutura']['ump_nao_organizada'] += (isset($formulario->estrutura['ump_nao_organizada']) ? intval($formulario->estrutura['ump_nao_organizada']) : 0);


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

                    
                    $totalizador['programacoes_federacao']['social'] += (isset($formulario->programacoes['social']) ? intval($formulario->programacoes['social']) : 0);
                    $totalizador['programacoes_federacao']['oracao'] += (isset($formulario->programacoes['oracao']) ? intval($formulario->programacoes['oracao']) : 0);
                    $totalizador['programacoes_federacao']['evangelistica'] += (isset($formulario->programacoes['evangelistica']) ? intval($formulario->programacoes['evangelistica']) : 0);
                    $totalizador['programacoes_federacao']['espiritual'] += (isset($formulario->programacoes['espiritual']) ? intval($formulario->programacoes['espiritual']) : 0);
                    $totalizador['programacoes_federacao']['recreativo'] += (isset($formulario->programacoes['recreativo']) ? intval($formulario->programacoes['recreativo']) : 0);
                    
                    
                    $totalizador['programacoes_locais']['social'] += (isset($formulario->programacoes_locais['social']) ? intval($formulario->programacoes_locais['social']) : 0);
                    $totalizador['programacoes_locais']['oracao'] += (isset($formulario->programacoes_locais['oracao']) ? intval($formulario->programacoes_locais['oracao']) : 0);
                    $totalizador['programacoes_locais']['evangelistica'] += (isset($formulario->programacoes_locais['evangelistica']) ? intval($formulario->programacoes_locais['evangelistica']) : 0);
                    $totalizador['programacoes_locais']['espiritual'] += (isset($formulario->programacoes_locais['espiritual']) ? intval($formulario->programacoes_locais['espiritual']) : 0);
                    $totalizador['programacoes_locais']['recreativo'] += (isset($formulario->programacoes_locais['recreativo']) ? intval($formulario->programacoes_locais['recreativo']) : 0);
            }
            $totalizador['estrutura']['federacao_organizada'] = Federacao::where('sinodal_id', $id)->where('status', 1)->get()->count();
            $totalizador['estrutura']['federacao_nao_organizada'] = Federacao::where('sinodal_id', $id)->where('status', 0)->get()->count();

            return $totalizador;
        } catch (\Throwable $th) {
            throw new Exception("Erro no Totalizador", 1);
            
        }
    }
}