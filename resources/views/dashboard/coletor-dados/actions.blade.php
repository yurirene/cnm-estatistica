<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

        <button href="#"
            class="dropdown-item"
            onclick="apagar('{{$id}}')"
        >
            Remover
        </button>
        @if(!empty($resposta))
        <button type="button" data-target="#modal-resposta" data-resposta="{{ $resposta }}" data-toggle="modal"
            class="dropdown-item"
        >
            Resposta
        </button>
        @endif
    </div>
</div>
