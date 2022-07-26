<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        @if(isset($show) && $show == true)
        <a class="dropdown-item" href="{{ route($route.'.show', $id) }}">Visualizar</a>
        @endif 
        @if(isset($confirmar) && $confirmar == true)
        <a class="dropdown-item" href="{{ route($route.'.confirmar', $id) }}">Confirmar</a>
        @endif
        @if(!isset($edit) || (isset($edit) && $edit == true))
        <a class="dropdown-item" href="{{ route($route.'.edit', $id) }}">Editar</a>
        @endif
        @if(!isset($delete) || (isset($delete) && $delete == true))
        <button class="dropdown-item" href="#" onclick="deleteRegistro('{{ route($route.'.delete', $id) }}')">Apagar</button>
        @endif
        @if(isset($abrir))
        <a class="dropdown-item" target="_blank" href="{{ $abrir }}">Abrir</a>
        @endif
        @if(isset($status) && $status == true)
        <a class="dropdown-item" href="{{ route($route.'.status', $id) }}">Alterar Status</a>
        @endif
        @if(isset($configuracoes) && $configuracoes == true)
        <a class="dropdown-item" href="{{ route($route.'.configuracoes', $id) }}">Configurações</a>
        @endif
        @if(isset($relatorio) && $relatorio == true)
        <a class="dropdown-item" href="{{ route($route.'.relatorio', $id) }}">Relatório</a>
        @endif
        @if(isset($acompanhar) && $acompanhar == true)
        <a class="dropdown-item" href="{{ route($route.'.acompanhar', $id) }}">Acompanhar</a>
        @endif
    </div>
</div>
