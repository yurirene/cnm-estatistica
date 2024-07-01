{!! Form::open(
    [
        'url' => route('dashboard.apps.tesouraria.categoria.store'),
        'method' => 'POST',
        'files' => false
    ]
) !!}
<div class="row">

    <div class="col-md-8">
        <div class="form-group">
            {!! Form::label('nome', 'Nome') !!}
            {!! Form::text('nome', null, ['class' => 'form-control', 'required' => true, 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-group">
            <button class="btn btn-primary">
                <em class="fas fa-save"></em>
                Cadastrar
            </button>
        </div>
    </div>

</div>
{!! Form::close() !!}
<div class="row">
    <div class="col">
        <div class="table-responsive">
            <table class="table" id="table-categorias">
                <thead>
                    <th>Ações</th>
                    <th>Nome</th>
                </thead>
                <tbody>
                @foreach($categorias as $id => $categoria)
                <tr>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle"
                                type="button" data-toggle="dropdown"
                            >
                                Ações
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{ route('dashboard.apps.tesouraria.categoria.edit', $id) }}"
                                    class="dropdown-item confirmar_categoria"
                                >
                                    Editar
                                </a>
                                <a href="#" onclick="deleteRegistro('{{ route('dashboard.apps.tesouraria.categoria.delete', $id) }}')"
                                    class="dropdown-item remover_categoria"
                                >
                                    Remover
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="status_coluna">{!! $categoria !!}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
