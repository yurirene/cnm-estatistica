<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFederacaoRequest extends FormRequest
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
            'nome' => ['required'],
            'sigla' => ['required'],
            'status' => ['required'],
            'email_usuario' => ['required', 'unique:users,email'],
            'nome_usuario' => ['required'],
            'estado_id' => ['required'],
            'sinodal_id' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O Nome da Federação é obrigatório',
            'sigla.required' => 'A Sigla da Federação é obrigatório',
            'status.required' => 'O Status é obrigatório',
            'email_usuario.required' => 'O E-mail do usuário é obrigatório',
            'email_usuario.unique' => 'O E-mail já está sendo utilizado por outra Federação. Altere a sigla',
            'nome_usuario.required' => 'O Nome do Usuário é obrigatório',
            'estado_id.required' => 'O Estado da Federação é obrigatório'
        ];
    }
}
