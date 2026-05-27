<?php

namespace App\Http\Requests\Tarefas;

use App\Enums\TarefaPeriodoNotificacao;
use App\Enums\TarefaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'prazo_final' => ['nullable', 'date', 'after_or_equal:today'],
            'periodo_notificacao' => ['required', Rule::enum(TarefaPeriodoNotificacao::class)],
            'status' => ['sometimes', Rule::enum(TarefaStatus::class)],
        ];
    }
}
