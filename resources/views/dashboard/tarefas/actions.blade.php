<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <div class="dropdown-menu">
        <button
            type="button"
            class="dropdown-item btn-editar-tarefa"
            data-toggle="modal"
            data-target="#modal-tarefa"
            data-acao="editar"
            data-id="{{ $tarefa->id }}"
            data-titulo="{{ $tarefa->titulo }}"
            data-descricao="{{ $tarefa->descricao }}"
            data-prazo-final="{{ $tarefa->prazo_final?->format('Y-m-d') }}"
            data-periodo="{{ $tarefa->periodo_notificacao->value }}"
            data-status="{{ $tarefa->status->value }}"
        >
            Editar
        </button>
        @if($tarefa->status->value === 'pendente')
        <a href="{{ route('dashboard.tarefas.encerrar', $tarefa) }}"
            class="dropdown-item text-success"
            onclick="return confirm('Marcar esta tarefa como concluída?');"
        >
            Concluir
        </a>
        @endif
        <button type="button"
            class="dropdown-item text-danger"
            onclick="deleteRegistro('{{ route('dashboard.tarefas.delete', $tarefa) }}')"
        >
            Remover
        </button>
    </div>
</div>
