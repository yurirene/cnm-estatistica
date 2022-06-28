e<table class="table align-items-center table-flush" id="tabela-formulario-federacoes">
    <thead class="thead-light">
        <tr>
            <th scope="col">Ação</th>
            <th scope="col">Federação</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($federacoes as $federacao)
        <tr>
            <td scope="row"> 
                <button class="btn btn-primary btn-sm btn-info-sinodal" 
                    data-sinodal='{{ $federacao['id'] }}'>
                        <i class="fas fa-eye"></i>
                </button>
            </td>
            <th scope="row"> {{$federacao['nome']}} </th>
            <td> {!! $federacao['status'] !!} </td>
        </tr>
        @empty
        <tr>
            <th scope="row" colspan="2">
                Sem Federações Cadastradas
            </th>
        </tr>
        @endforelse
    </tbody>
</table>