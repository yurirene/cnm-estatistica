<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormularioLocalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'perfil' => ['array', 'required', 'min:8'],
            'escolaridade' => ['array', 'required', 'min:6'],
            'estado_civil' => ['array', 'required', 'min:5'],
            'deficiencia' => ['array', 'required', 'min:9'],
            'programacoes' => ['array', 'required', 'min:5'],
            'aci' => ['array', 'required', 'min:2']
        ];
    }

    public function messages()
    {
        return [
            'perfil.*.required' => 'O :attribute é obrigatório',

            'escolaridade.*.required' => 'O :attribute é obrigatório',
            'estado_civil.*.required' => 'O :attribute é obrigatório',
            
            'deficiencia.surdos.required' => 'O :attribute é obrigatório',
            'deficiencia.auditiva.required' => 'O :attribute é obrigatório',
            'deficiencia.cegos.required' => 'O :attribute é obrigatório',
            'deficiencia.baixa_visao.required' => 'O :attribute é obrigatório',
            'deficiencia.fisica_inferior.required' => 'O :attribute é obrigatório',
            'deficiencia.fisica_superior.required' => 'O :attribute é obrigatório',
            'deficiencia.neurologico.required' => 'O :attribute é obrigatório',
            'deficiencia.intelectual.required' => 'O :attribute é obrigatório',

            'programacoes.*.required' => 'O :attribute é obrigatório',

            'aci.repasse.required' => 'O :attribute é obrigatório',
        ];
    }

    public function attributes()
    {
        return [
            'perfil.ativo' => 'Ativos',
            'perfil.cooperadores' => 'Cooperadores',
            'perfil.menor19' => 'menores de 19 anos',
            'perfil.de19a23' => 'entre 19-23 anos',
            'perfil.de24a29' => 'entre 24-29 anos',
            'perfil.de30a35' => 'entre 30-35 anos',
            'perfil.homens' => '- Homens',
            'perfil.mulheres' => '- Mulheres',

            'deficiencia.surdos' => 'Surdos',
            'deficiencia.auditiva' => 'Deficiência auditiva',
            'deficiencia.cegos' => 'Cegos',
            'deficiencia.baixa_visao' => 'Baixa visão',
            'deficiencia.fisica_inferior' => 'Deficiência Física/Motora em membros inferiores',
            'deficiencia.fisica_superior' => 'Deficiência Física/Motora em membros superiores',
            'deficiencia.neurologico' => 'Algum transtorno neurológico',
            'deficiencia.intelectual' => 'Deficiência intelectual',

            'aci.repasse' => 'repasse da ACI para a Federação',

            'escolaridade.fundamental' => 'Ensino Fundamental',
            'escolaridade.medio' => 'Ensino Médio',
            'escolaridade.tecnico' => 'Ensino Técnico',
            'escolaridade.superior' => 'Ensino Superior',
            'escolaridade.pos' => 'Pós-Graduação',
            'escolaridade.desempregado' => 'Desempregado ',

            'estado_civil.solteiros' => 'Solteiro',
            'estado_civil.casados' => 'Casado',
            'estado_civil.divorciados' => 'Divorciado',
            'estado_civil.viuvos' => 'Viúvo',
            'estado_civil.filhos' => 'Filhos',

            'programacoes.social' => 'Social',
            'programacoes.evangelistico' => 'Evangelístico e Missional',
            'programacoes.espiritual' => 'Espiritual',
            'programacoes.recreativo' => 'Recreativo',
            'programacoes.oracao' => 'Reunião de Oração e Vigília',


        ];
    }
}
