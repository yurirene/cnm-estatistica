<?php

namespace App\Http\Requests\Arquivos;

use App\Services\GoogleDriveService;
use Illuminate\Foundation\Http\FormRequest;

class CriarPastaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return GoogleDriveService::podeAcessar($this->user())
            && GoogleDriveService::temAcesso($this->user());
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255', 'regex:/^[^\/\\\\]+$/'],
            'pasta' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome da pasta',
        ];
    }
}
