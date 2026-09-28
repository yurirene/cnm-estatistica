<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow p-3">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Editais</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($editais->isEmpty())
                    <p class="text-muted mb-0">Nenhum edital cadastrado.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Ano</th>
                                    <th>Prazo</th>
                                    <th>Recebimento</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($editais as $item)
                                <tr>
                                    <td>{{ $item->titulo }}</td>
                                    <td>{{ $item->ano }}</td>
                                    <td>{{ $item->data_limite_proposta->format('d/m/Y') }}</td>
                                    <td>
                                        @if($item->estaRecebendo())
                                            <span class="badge badge-success">Aberto</span>
                                        @else
                                            <span class="badge badge-danger">Encerrado</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('dashboard.auxilio-pmf.edit', $item->id) }}" class="btn btn-sm btn-info">
                                            Editar
                                        </a>
                                        @if($item->recebimento_aberto)
                                        <a href="{{ route('dashboard.auxilio-pmf.encerrar', $item->id) }}"
                                           class="btn btn-sm btn-warning"
                                           onclick="return confirm('Encerrar o recebimento de propostas deste edital?')">
                                            Encerrar recebimento
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-12 mb-5">
        <div class="card shadow p-3">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Propostas
                            @if($edital)
                                — {{ $edital->titulo }}
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(!$edital)
                    <p class="text-muted mb-0">Cadastre um edital para listar as propostas.</p>
                @else
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table w-100']) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
