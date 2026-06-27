<?php

namespace App\Http\Requests\Arquivos;

use App\Services\GoogleDriveService;
use Illuminate\Foundation\Http\FormRequest;

class RenomearItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return GoogleDriveService::podeAcessar($this->user())
            && GoogleDriveService::temAcesso($this->user());
    }

    public function rules(): array
    {
        return [
            'caminho' => ['required', 'string', 'max:500'],
            'nome' => ['required', 'string', 'max:255', 'regex:/^[^\/\\\\]+$/'],
            'tipo' => ['required', 'in:arquivo,pasta'],
            'pasta_atual' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
        ];
    }
}
