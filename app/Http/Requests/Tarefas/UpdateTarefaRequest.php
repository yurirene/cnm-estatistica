<?php

namespace App\Http\Requests\Tarefas;

use App\Enums\TarefaPeriodoNotificacao;
use App\Enums\TarefaStatus;
use App\Services\TarefaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tarefa = $this->route('tarefa');

        return TarefaService::pertenceAoUsuario($tarefa, $this->user());
    }

    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'prazo_final' => ['nullable', 'date'],
            'periodo_notificacao' => ['sometimes', Rule::enum(TarefaPeriodoNotificacao::class)],
            'status' => ['sometimes', Rule::enum(TarefaStatus::class)],
        ];
    }
}
