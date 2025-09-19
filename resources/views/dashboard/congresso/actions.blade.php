<div class="btn-group" role="group">
    <a href="{{ route($route . '.show', $id) }}" class="btn btn-sm btn-info" title="Visualizar">
        <i class="fas fa-eye"></i>
    </a>

    @if($podeEditar)
    <a href="{{ route($route . '.edit', $id) }}" class="btn btn-sm btn-warning" title="Editar">
        <i class="fas fa-edit"></i>
    </a>

    @if($delete)
    <form action="{{ route($route . '.destroy', $id) }}" method="POST" style="display: inline-block;"
          onsubmit="return confirm('Tem certeza que deseja excluir este congresso?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
            <i class="fas fa-trash"></i>
        </button>
    </form>
    @endif
    @endif
</div>
