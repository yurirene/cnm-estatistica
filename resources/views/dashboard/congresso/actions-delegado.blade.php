<div class="btn-group" role="group">
    <button type="button" class="btn btn-sm btn-info" title="Visualizar" onclick="visualizarDelegado('{{ $id }}')">
        <i class="fas fa-eye"></i>
    </button>

    <button type="button" class="btn btn-sm btn-warning" title="Editar" onclick="editarDelegado('{{ $id }}')">
        <i class="fas fa-edit"></i>
    </button>

    <form action="{{ route('dashboard.congresso.delegado.destroy', ['reuniao' => request()->route('reuniao'), 'delegado' => $id]) }}"
          method="POST" style="display: inline-block;"
          onsubmit="return confirm('Tem certeza que deseja remover este delegado?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Remover">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>

<script>
function visualizarDelegado(id) {
    // Implementar modal de visualização
    alert('Visualizar delegado: ' + id);
}

function editarDelegado(id) {
    // Implementar modal de edição
    alert('Editar delegado: ' + id);
}
</script>
