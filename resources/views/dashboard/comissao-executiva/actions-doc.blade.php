<div class="dropdown">
    <button
        class="btn btn-primary btn-sm dropdown-toggle"
        type="button"
        data-toggle="dropdown"
        aria-expanded="false"
    >
        Ações
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a href="/{{$url}}" class="dropdown-item" target="_blank">Abrir Documento</a>
        @if($delete)
        <button class="dropdown-item" onclick="deleteRegistro('{{ route('dashboard.ce-sinodal.remover-documento', $id) }}')">Apagar</button>
        @endif
    </div>
</div>
