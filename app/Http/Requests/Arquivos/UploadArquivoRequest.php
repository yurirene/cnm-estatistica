<?php

namespace App\Http\Requests\Arquivos;

use App\Services\GoogleDriveService;
use Illuminate\Foundation\Http\FormRequest;

class UploadArquivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return GoogleDriveService::podeAcessar($this->user())
            && GoogleDriveService::temAcesso($this->user());
    }

    public function rules(): array
    {
        return [
            'arquivo' => ['required', 'file', 'max:51200'],
            'pasta' => ['nullable', 'string', 'max:500'],
        ];
    }
}
