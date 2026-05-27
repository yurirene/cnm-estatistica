<?php

namespace App\Http\Requests\Resolucoes;

use Illuminate\Foundation\Http\FormRequest;

class ImportResolucaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'arquivo' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ];
    }
}
