<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

        <a href="{{ route($route.'.delete', $id) }}"
            class="dropdown-item"
        >
            Remover
        </a>

        <button type="button" data-target="#modal-visualizados" data-id="{{ $id }}" data-toggle="modal"
            class="dropdown-item"
        >
            Visualizados
        </button>
    </div>
</div>
