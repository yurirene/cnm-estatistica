<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        @if($abrir)
        <a class="dropdown-item" href="{{ route($route.'.show', $id) }}">Abrir</a>
        @endif
    </div>
</div>
