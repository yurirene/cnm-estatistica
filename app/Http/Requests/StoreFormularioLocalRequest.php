<?php

namespace App\Http\Requests;

use App\Services\Formularios\ValidarFormularioService;
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
    protected function prepareForValidation()
    {
        $this->merge([
            'total' => intval($this->perfil['ativos']) + intval($this->perfil['cooperadores']),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $response  = $this->somatorio();
            if (!$response['status']) {
                $validator->errors()->add('somatorio', $response['text']);
            }
        });
    }

    public function somatorio()
    {

        $response = [
            'status' => true,
            'text' => ''
        ];

        $validacoes = [];
        $erros = [];
        
        $validacoes['Sexo'] = ValidarFormularioService::somatorio(
            $this->total, 
            $this->perfil['homens'], 
            $this->perfil['mulheres']
        );
        $validacoes['Idade'] = ValidarFormularioService::somatorio(
            $this->total, 
            $this->perfil['menor19'], 
            $this->perfil['de19a23'],
            $this->perfil['de24a29'],
            $this->perfil['de30a35']
        );
        $validacoes['Escolaridade'] = ValidarFormularioService::somatorio(
            $this->total, 
            $this->escolaridade['fundamental'], 
            $this->escolaridade['medio'],
            $this->escolaridade['tecnico'],
            $this->escolaridade['superior'],
            $this->escolaridade['pos']
        );
        
        $validacoes['Estado Civil'] = ValidarFormularioService::somatorio(
            $this->total, 
            $this->estado_civil['solteiros'], 
            $this->estado_civil['casados'],
            $this->estado_civil['divorciados'],
            $this->estado_civil['viuvos']
        );

        
        $validacoes['S??cios com Filhos'] = ValidarFormularioService::limite(
            $this->total, 
            $this->estado_civil['filhos']
        );    
        
        $validacoes['Desempregados'] = ValidarFormularioService::limite(
            $this->total, 
            $this->escolaridade['desempregado']
        );

        foreach ($validacoes as $campo => $v) {
            if (!$v) {
                $response['status'] = false;
                $erros[] = $campo;
            }
        }
        if (!$response['status']) {
            $texto = count($erros) > 1 ? 'Os campos [ :campo ] n??o totalizam :total s??cios' : 'O campo :campo n??o totaliza :total s??cios';
            $response['text'] = str_replace([':campo', ':total'], [implode(', ', $erros), $this->total], $texto);
        }

        return $response;

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
            'estrutura' => ['array', 'required'],
            'escolaridade' => ['array', 'required', 'min:6'],
            'estado_civil' => ['array', 'required', 'min:5'],
            'deficiencias' => ['array', 'required', 'min:9'],
            'programacoes' => ['array', 'required', 'min:5'],
            'aci' => ['array', 'required', 'min:2'],
            'perfil.*' => ['min:0'],
            'escolaridade.*' => ['min:0'],
            'estado_civil.*' => ['min:0'],
            'deficiencias.*' => ['min:0'],
            'programacoes.*' => ['min:0'],
            
        ];
    }

    public function messages()
    {
        return [
            'perfil.*.required' => 'O :attribute ?? obrigat??rio',

            'escolaridade.*.required' => 'O :attribute ?? obrigat??rio',
            'estado_civil.*.required' => 'O :attribute ?? obrigat??rio',
            
            'deficiencia.surdos.required' => 'O :attribute ?? obrigat??rio',
            'deficiencia.auditiva.required' => 'O :attribute ?? obrigat??rio',
            'deficiencia.cegos.required' => 'O :attribute ?? obrigat??rio',
            'deficiencia.baixa_visao.required' => 'O :attribute ?? obrigat??rio',
            'deficiencia.fisica_inferior.required' => 'O :attribute ?? obrigat??rio',
            'deficiencia.fisica_superior.required' => 'O :attribute ?? obrigat??rio',
            'deficiencia.neurologico.required' => 'O :attribute ?? obrigat??rio',
            'deficiencia.intelectual.required' => 'O :attribute ?? obrigat??rio',

            'programacoes.*.required' => 'O :attribute ?? obrigat??rio',

            'aci.repasse.required' => 'O :attribute ?? obrigat??rio',
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
            'deficiencia.auditiva' => 'Defici??ncia auditiva',
            'deficiencia.cegos' => 'Cegos',
            'deficiencia.baixa_visao' => 'Baixa vis??o',
            'deficiencia.fisica_inferior' => 'Defici??ncia F??sica/Motora em membros inferiores',
            'deficiencia.fisica_superior' => 'Defici??ncia F??sica/Motora em membros superiores',
            'deficiencia.neurologico' => 'Algum transtorno neurol??gico',
            'deficiencia.intelectual' => 'Defici??ncia intelectual',

            'aci.repasse' => 'repasse da ACI para a Federa????o',

            'escolaridade.fundamental' => 'Ensino Fundamental',
            'escolaridade.medio' => 'Ensino M??dio',
            'escolaridade.tecnico' => 'Ensino T??cnico',
            'escolaridade.superior' => 'Ensino Superior',
            'escolaridade.pos' => 'P??s-Gradua????o',
            'escolaridade.desempregado' => 'Desempregado ',

            'estado_civil.solteiros' => 'Solteiro',
            'estado_civil.casados' => 'Casado',
            'estado_civil.divorciados' => 'Divorciado',
            'estado_civil.viuvos' => 'Vi??vo',
            'estado_civil.filhos' => 'Filhos',

            'programacoes.social' => 'Social',
            'programacoes.evangelistico' => 'Evangel??stico e Missional',
            'programacoes.espiritual' => 'Espiritual',
            'programacoes.recreativo' => 'Recreativo',
            'programacoes.oracao' => 'Reuni??o de Ora????o e Vig??lia',


        ];
    }
}
