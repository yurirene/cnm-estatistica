<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ações</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Oficial</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($delegados as $delegado)
                <tr>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $delegado->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                Ações
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $delegado->id }}">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.cn.sinodal.delegado.edit', $delegado->id) }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('dashboard.cn.sinodal.delegado.delete', $delegado->id) }}">
                                        <i class="fas fa-trash"></i> Excluir
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>{{ $delegado->nome }}</td>
                    <td>{{ $delegado->telefone }}</td>
                    <td>
                        @if($delegado->oficial == 1)
                            Diácono
                        @elseif($delegado->oficial == 2)
                            Presbítero
                        @else
                            Não
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum delegado cadastrado</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
