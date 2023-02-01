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
    </div>
</div>
