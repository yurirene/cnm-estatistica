<?php

namespace App\Http\Requests\AuxilioPmf;

use App\Services\AuxilioPmfService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEditalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return AuxilioPmfService::isAdmin($this->user());
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'ano' => ['required', 'integer', 'min:2000', 'max:2100'],
            'data_limite_proposta' => ['required', 'date'],
            'link_edital' => ['required', 'url', 'max:500'],
            'link_proposta' => ['required', 'url', 'max:500'],
            'link_relatorio' => ['required', 'url', 'max:500'],
        ];
    }
}
