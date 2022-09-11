<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        @if(!isset($edit) || (isset($edit) && $edit == true))
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-edit-item" data-item="{{ $item }}">Editar</a>
        @endif
        @if(!isset($delete) || (isset($delete) && $delete == true))
        <button class="dropdown-item" href="#" onclick="deleteRegistro('{{ route($route.'.delete-item', [$demanda_id, $id]) }}')">Apagar</button>
        @endif
    </div>
</div>
