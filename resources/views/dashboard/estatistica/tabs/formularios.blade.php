<div class="tab-pane fade show active" id="terceiro" role="tabpanel" aria-labelledby="terceiro-tab">
    <div class="row mt-3">
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <a 
                                href="{{ route('dashboard.estatistica.atualizar-ranking') }}" 
                                class="btn btn-primary"
                            >
                            Atualizar Lista
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="formularios-table" class="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sinodal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Federações</th>
                                        <th class="text-center">UMP Locais</th>
                                        <th class="text-center">Qualidade</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('js')

<script>
    $(function() {
        $('#formularios-table').DataTable({
            dom: 'frtip',
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.estatistica.formularios-sinodais") }}',
            columns: [
                {data: 'nome'},
                {
                    render: function (data, type, result) {
                        return `<span class="badge bg-${result.entregue == 1 ? 'success' : 'danger'}">
                            ${result.entregue == 1 ? 'Entregue' : 'Pendente'}
                        </span>`;
                    }
                },
                {
                    render: function (data, type, result) {
                        return `<a
                            href="#"
                            class=""
                            data-toggle="modal"
                            data-target="#federacoes-modal"
                            data-id="${result.id}">
                                ${result.federacoes}
                            </a>`;
                    }
                },
                {
                    render: function (data, type, result) {
                        return `<a
                            href="#"
                            class=""
                            data-toggle="modal"
                            data-target="#locais-modal"
                            data-id="${result.id}">
                                ${result.locais}
                            </a>`;
                    }
                },
                {data: 'qualidade'},
            ]
        });
    });


    $('#sinodal-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var route = '{{ route("dashboard.datatables.formularios-entregues", ["instancia" => "Federacao", "id" => ":id"]) }}'.replace(':id', id);
        carregarDataTableFederacao(route);
    });

    $('#local-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var route = '{{ route("dashboard.datatables.formularios-entregues", ["instancia" => "Local", "id" => ":id"]) }}'.replace(':id', id);
        carregarDataTableLocal(route);
    });

    function carregarDataTableFederacao(route) {

        $('#federacao-entregues-table').DataTable().destroy();
        $('#federacao-entregues-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: route,
            columns: [
                {
                    render: function (data, type, result) {
                        return `<button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-toggle="modal"
                            data-target="#local-modal"
                            data-id="${result.id}">
                                <i class="fas fa-eye"></i>
                            </button>`;
                    }
                },
                {data: 'nome'},
                {
                    render: function (data, type, result) {
                        return `<span class="badge bg-${result.entregue == 1 ? 'success' : 'danger'}">
                            ${result.entregue == 1 ? 'Entregue' : 'Pendente'}
                        </span>`;
                    }
                },
            ]
        });
    }

function carregarDataTableLocal(route) {

    $('#local-entregues-table').DataTable().destroy();
    $('#local-entregues-table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: route,
        columns: [
            {data: 'nome'},
            {
                render: function (data, type, result) {
                    return `<span class="badge bg-${result.entregue == 1 ? 'success' : 'danger'}">
                        ${result.entregue == 1 ? 'Entregue' : 'Pendente'}
                    </span>`;
                }
            },
        ]
    });
}

</script>
@endpush