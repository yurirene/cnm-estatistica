<?php

namespace App\Http\Requests\Resolucoes;

use App\Enums\ResolucaoOrigem;
use App\Enums\ResolucaoPrioridade;
use App\Enums\ResolucaoStatus;
use App\Services\ResolucaoService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResolucaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $resolucao = $this->route('resolucao');

        return ResolucaoService::isGestor($this->user())
            || ($resolucao?->responsavel_id && $resolucao->responsavel_id === $this->user()->id);
    }

    public function rules(): array
    {
        $dataAprovacao = $this->input('data_aprovacao', $this->route('resolucao')?->data_aprovacao?->format('Y-m-d'));

        return [
            'titulo' => ['sometimes', 'string', 'max:255'],
            'descricao' => ['sometimes', 'string'],
            'origem' => ['sometimes', Rule::enum(ResolucaoOrigem::class)],
            'status' => ['sometimes', Rule::enum(ResolucaoStatus::class)],
            'prioridade' => ['sometimes', Rule::enum(ResolucaoPrioridade::class)],
            'data_aprovacao' => ['sometimes', 'date'],
            'prazo_final' => ['nullable', 'date', 'after_or_equal:' . ($dataAprovacao ?? 'today')],
            'responsavel_id' => ['nullable', 'uuid', 'exists:users,id'],
            'anexos' => ['nullable', 'array'],
            'anexos.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
            'remover_anexos' => ['nullable', 'array'],
            'remover_anexos.*' => ['string'],
        ];
    }
}
