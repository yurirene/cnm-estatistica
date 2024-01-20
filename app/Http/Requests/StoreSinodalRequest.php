<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSinodalRequest extends FormRequest
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
            'nome_usuario' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O Nome da Sinodal é obrigatório',
            'sigla.required' => 'A Sigla da Sinodal é obrigatório',
            'status.required' => 'O Status é obrigatório',
            'email_usuario.required' => 'O E-mail do usuário é obrigatório',
            'email_usuario.unique' => 'O E-mail já está sendo utilizado por outra Sinodal. Altere a sigla',
            'nome_usuario.required' => 'O Nome do Usuário é obrigatório',
        ];
    }
}
