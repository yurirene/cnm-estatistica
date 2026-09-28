<?php

namespace App\Http\Requests\AuxilioPmf;

use App\Services\AuxilioPmfService;
use Illuminate\Foundation\Http\FormRequest;

class StorePropostaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return AuxilioPmfService::isSinodal($this->user());
    }

    public function rules(): array
    {
        return [
            'arquivo' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'arquivo.required' => 'Selecione um arquivo PDF.',
            'arquivo.mimes' => 'O arquivo deve ser um PDF.',
        ];
    }
}
