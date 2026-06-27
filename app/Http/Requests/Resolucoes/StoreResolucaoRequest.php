<?php

namespace App\Http\Requests\Resolucoes;

use App\Enums\ResolucaoOrigem;
use App\Enums\ResolucaoPrioridade;
use App\Enums\ResolucaoStatus;
use App\Services\ResolucaoService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResolucaoRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nao_notificar' => $this->boolean('nao_notificar'),
        ]);
    }

    public function authorize(): bool
    {
        return ResolucaoService::isGestor($this->user());
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'origem' => ['required', Rule::enum(ResolucaoOrigem::class)],
            'status' => ['sometimes', Rule::enum(ResolucaoStatus::class)],
            'prioridade' => ['required', Rule::enum(ResolucaoPrioridade::class)],
            'data_aprovacao' => ['required', 'date'],
            'prazo_final' => ['nullable', 'date', 'after_or_equal:data_aprovacao'],
            'responsavel_id' => ['nullable', 'uuid', 'exists:users,id'],
            'nao_notificar' => ['sometimes', 'boolean'],
            'anexos' => ['nullable', 'array'],
            'anexos.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ];
    }
}
