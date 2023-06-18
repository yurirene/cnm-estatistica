<div
    class="tab-pane fade {{ session()->has('aba') && session('aba') == 'inscritos' ? 'show active' : ''}}"
    id="terceiro" role="tabpanel" aria-labelledby="terceiro-tab"
>
    <div class="row">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card p-3 border-0">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Lista de Inscritos</h3>
                        </div>
                        <div class="col text-end">
                            <button
                                class="btn btn-danger"
                                style="border-radius: 25px;"
                                id="limpar_lista"
                            >
                                Limpar Lista
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table" id="table-inscritos">
                                    <thead>
                                        <th>Ações</th>
                                        @foreach($evento->form as $campo)
                                        <th>{{ $campo['campo'] }}</th>
                                        @endforeach
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                                    @foreach($inscritos as $inscrito)
                                    <tr>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-primary btn-sm dropdown-toggle"
                                                    type="button" data-toggle="dropdown"
                                                >
                                                    Ações
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="#" data-id="{{$inscrito['id']}}"
                                                        class="dropdown-item confirmar_inscrito"
                                                    >
                                                        Alterar Status
                                                    </a>
                                                    <a href="#" data-id="{{$inscrito['id']}}"
                                                        class="dropdown-item remover_inscrito"
                                                    >
                                                        Remover
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($inscrito as $key => $info)
                                            @if(!in_array($key, ['id', 'status']))
                                                <td>{{$info}}</td>
                                            @endif
                                        @endforeach
                                        <td class="status_coluna">{!! $inscrito['status'] !!}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    $('#table-inscritos').DataTable({
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        stateSave: true
    });

    $('.confirmar_inscrito').on('click', function() {
        let id = $(this).data('id');
        const URL = '{{route("dashboard.apps.sites.status-inscrito", ["evento_id" => $evento->id, "inscrito_id" => ":id"])}}';
        $.ajax({
            url: URL.replace(':id', id),
            success: (response) => {
                iziToast.success({
                    message: 'Status alterado com sucesso!',
                    position: 'topRight',
                });
                $(this).closest('tr').find('.status_coluna').text(response.status)
            },
            error: (error) => {
                iziToast.error({
                    title: 'Erro!',
                    message: 'Erro ao alterar o status',
                    position: 'topRight',
                });
            }
        });
    });
    $('.remover_inscrito').on('click', function() {
        let id = $(this).data('id');
        const URL_INSCRITO = '{{route("dashboard.apps.sites.remover-inscrito", ["evento_id" => $evento->id, "inscrito_id" => ":id"])}}';
        deleteRegistro(URL_INSCRITO.replace(':id', id));
    });

    $('#limpar_lista').on('click', function() {
        const URL_LIMPAR_LISTA = '{{route("dashboard.apps.sites.limpar-lista", $evento->id)}}';
        deleteRegistro(URL_LIMPAR_LISTA);
    })
</script>
@endpush
