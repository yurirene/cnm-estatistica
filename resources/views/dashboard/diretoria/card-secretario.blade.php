<div class="col-md-6 p-3">
    <div class="card card-stats">
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <h5 class="card-title text-uppercase text-muted mb-1">
                        {{ $secretario['secretaria'] }}
                    </h5>
                    <span class="h3 font-weight-bold">{{ $secretario['nome'] }}</span>
                    <p class="text-sm mb-0">
                        <span class="text-nowrap">{{ $secretario['contato'] }}</span>
                    </p>
                    <button
                        class="btn btn-sm btn-warning mt-2"
                        data-toggle="modal"
                        data-target="#modal-edicao-secretario"
                        data-nome="{{ $secretario['nome'] }}"
                        data-path="{{ $secretario['path'] }}"
                        data-secretaria="{{ $secretario['secretaria'] }}"
                        data-contato="{{ $secretario['contato'] }}"
                        data-id="{{ $secretario['id'] }}"
                    >
                        <i class="fas fa-sync"></i>
                        Atualizar
                    </button>
                    <button
                        class="btn btn-danger btn-sm mt-2 remover-secretario"
                        data-id="{{ $secretario['id'] }}"
                        data-secretaria="{{ $secretario['secretaria'] }}"
                    >
                        <i class="fas fa-trash"></i>
                        Remover
                    </button>
                </div>
                <div class="col-4">
                    <img
                        src="/{{ $secretario['path'] }}"
                        class="img-fluid rounded-start"
                        alt="..."
                    >
                </div>
            </div>
        </div>
    </div>
</div>
