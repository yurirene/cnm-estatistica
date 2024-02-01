<div class="col-md-6 p-3">
    <div class="card card-stats">
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <h5 class="card-title text-uppercase text-muted mb-1">
                        {{ $membro['cargo'] }}
                    </h5>
                    <span class="h3 font-weight-bold">{{ $membro['nome'] }}</span>
                    <p class="text-sm mb-0">
                        <span class="text-nowrap">{{ $membro['contato'] }}</span>
                    </p>
                    <button
                        class="btn btn-sm btn-warning"
                        data-toggle="modal"
                        data-target="#modal-edicao"
                        data-cargo="{{ $membro['cargo'] }}"
                        data-nome="{{ $membro['nome'] }}"
                        data-path="{{ $membro['path'] }}"
                        data-contato="{{ $membro['contato'] }}"
                        data-chave="{{ $chave }}"
                    >
                        <i class="fas fa-sync"></i>
                        Atualizar
                    </button>
                </div>
                <div class="col-4">
                    <img
                        src="/{{ $membro['path'] }}"
                        class="img-fluid rounded-start"
                        alt="..."
                    >
                </div>
            </div>
        </div>
    </div>
</div>
