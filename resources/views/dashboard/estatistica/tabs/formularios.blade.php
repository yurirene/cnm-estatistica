<div class="tab-pane fade show active" id="terceiro" role="tabpanel" aria-labelledby="terceiro-tab">
    <div class="row mt-3">
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="formularios-table" class="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sinodal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Federações</th>
                                        <th class="text-center">UMP Locais</th>
                                        <th class="text-center">
                                            Qualidade
                                            <sup>
                                                <em
                                                    class="fas fa-1x fa-info-circle"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Representa a porcentagem de UMPs Locais das Federações que enviaram o relatório para a Sinodal"
                                                ></em>
                                            </sup>
                                        </th>
                                        <th class="text-center">Região</th>
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
<div class="modal fade"
    id="federacoes-modal" tabindex="-1" role="dialog"
    aria-labelledby="locais-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="locais-modalLabel">Lista de Federações</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="federacoes-table" class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-center">Federação</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
</div>
<div class="modal fade"
    id="locais-modal" tabindex="-1" role="dialog"
    aria-labelledby="locais-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="locais-modalLabel">Lista de UMPs Locais</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="local-table" class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-center">UMP Local</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
</div>
@push('js')
<script src="/vendor/datatables/buttons.html5.min.js"></script>
<script>
    const ROUTE_ATUALIZAR_LISTA = "{{ route('dashboard.estatistica.atualizar-ranking') }}";
    $(function() {
        $('#formularios-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Atualizar Lista',
                    className: 'bg-primary text-white',
                    action: function ( e, dt, node, config ) {
                        window.location.href = ROUTE_ATUALIZAR_LISTA;
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: 'Exportar',
                }
            ],
            order: [4, 'desc'],
            destroy: true,
            pageLength: 50,
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
                {data: 'regiao'},
            ]
        });
    });


    $('#federacoes-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var route = '{{ route("dashboard.datatables.formularios-entregues", ["instancia" => "Federacao", "id" => ":id"]) }}'.replace(':id', id);
        carregarDataTableFederacao(route);
    });

    $('#locais-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var route = '{{ route("dashboard.datatables.estatistica.formularios-locais", ["id" => ":id"]) }}'.replace(':id', id);
        carregarDataTableLocal(route);
    });

    function carregarDataTableFederacao(route) {

        $('#federacoes-table').DataTable().destroy();
        $('#federacoes-table').DataTable({
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

function carregarDataTableLocal(route) {
    $('#local-table').DataTable().destroy();
    $('#local-table').DataTable({
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
