<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <div class="dropdown-menu">
        <button
            type="button"
            class="dropdown-item btn-editar-resolucao"
            data-toggle="modal"
            data-target="#modal-resolucao"
            data-acao="editar"
            data-id="{{ $resolucao->id }}"
            data-titulo="{{ $resolucao->titulo }}"
            data-descricao="{{ $resolucao->descricao }}"
            data-origem="{{ $resolucao->origem->value }}"
            data-status="{{ $resolucao->status->value }}"
            data-prioridade="{{ $resolucao->prioridade->value }}"
            data-data-aprovacao="{{ $resolucao->data_aprovacao?->format('Y-m-d') }}"
            data-prazo-final="{{ $resolucao->prazo_final?->format('Y-m-d') }}"
            data-responsavel-id="{{ $resolucao->responsavel_id }}"
            data-responsavel-text="{{ $resolucao->responsavel ? $resolucao->responsavel->name . ' (' . $resolucao->responsavel->email . ')' : '' }}"
            data-nao-notificar="{{ $resolucao->nao_notificar ? '1' : '0' }}"
        >
            Editar
        </button>
        @if($podeGerenciar)
        <a href="{{ route('dashboard.secretaria-executiva.resolucoes.delete', $resolucao) }}"
            class="dropdown-item text-danger"
            onclick="return confirm('Deseja remover esta resolução?');"
        >
            Remover
        </a>
        @endif
    </div>
</div>
