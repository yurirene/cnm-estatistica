@extends('layouts.app')

@section('content')
    @include('dashboard.index.diretoria.cards', [
        'totalizador' => DashboardHelper::getTotalizadores()
    ])

    <div class="container-fluid mt--7">

        <div class="row">
            <div class="col-xl-5 mb-5 mb-xl-0">
                <div class="card bg-gradient-default shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-light ls-1 mb-1">
                                    Qualidade da Entrega dos Formulários Estatísticos
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart -->
                        <div class="">
                            <!-- Chart wrapper -->
                            <canvas id="entregas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Estados do Usuário</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="mapa-brazil" style="margin: 0 auto; width: 460px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 mt-3">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class=" mb-0">Entrega de Formulários</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive">
                                    <table id="sinodal-entregues-table" class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Sinodal</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Federações</th>
                                                <th class="text-center">Locais</th>
                                                <th class="text-center">ACI Repassada</th>
                                                <th class="text-center">ACI Mínima</th>
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
    </div>

<div class="modal fade"
    id="sinodal-modal" tabindex="-1" role="dialog"
    aria-labelledby="sinodal-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sinodal-modalLabel">[Federação] - Formulários</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="federacao-entregues-table" class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
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
    id="local-modal" tabindex="-1" role="dialog"
    aria-labelledby="local-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="local-modalLabel">[UMP Local] - Formulários</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="local-entregues-table" class="table w-100">
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
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>

    <script>
        Highcharts.mapChart('mapa-brazil', {
            chart: {
                map: 'countries/br/br-all',
                height: 470
            },

            title: {
                text: ''
            },

            credits: {
                enabled: false
            },

            navigation: {
                buttonOptions: {
                    enabled: false
                }
            },

            series: [{
                borderColor: '#666',
                borderWidth: 0.4,
                data: {!! json_encode($dataMapaBrazil) !!},
                keys: ['id', 'n_socios', 'n_umps', 'n_federacoes'],
                joinBy: ['hc-key'],
                tooltip: {
                    pointFormatter: function() {
                        var hoverVotes = this.hoverVotes; // Used by pie only
                        return this.name + '<br/>' +
                            Highcharts.map(
                                [
                                    ['Nº de Sócios', this.n_socios],
                                    ['Nº de UMPs', this.n_umps],
                                    ['Nº de Federações', this.n_federacoes]

                                ],
                                 function(line) {
                                    return '<span style="color:' + line[2] +
                                        // Colorized bullet
                                        '">\u25CF</span> ' +
                                        // Party and votes
                                        (line[0] === hoverVotes ? '<b>' : '') +
                                        line[0] + ': ' +
                                        Highcharts.numberFormat(line[1], 0) +
                                        (line[0] === hoverVotes ? '</b>' : '') +
                                        '<br/>';
                                }
                            ).join('')
                    }
                },
                name: 'Estados do Usuário',
                states: {
                    hover: {
                        color: '#BADA55'
                    },
                    select: {
                        color: 'gray'
                    }
                },
                allowPointSelect: true,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}',
                    style: {
                        fontSize: '7px',
                        textOutline: '0px',
                        fontWeight: 'normal'
                    },
                }
            }]
        });
    </script>
@endpush
@push('script')
<script>
    const config_grafico_entrega = {
        type: 'doughnut',
        data: @json(DashboardHelper::getQualidadeEntregaRelatorios()),
        options: {
            responsive: true,
            plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Entrega dos Formulários de UMPs Locais (%)'
            }
            }
        },
    };

    const entregaChart = new Chart(
        document.getElementById('entregas'),
        config_grafico_entrega
    );

    $('#tabela-formulario').dataTable();


</script>
@endpush

@push('js')

<script>
    $(function() {
        var rotaExport = "{{ route('dashboard.formularios-sinodal.export', ':id') }}";
        $('#sinodal-entregues-table').DataTable({
            dom: 'frtipl',
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.formularios-entregues", "Sinodal") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        var imprimir = '';
                        if (result.entregue == 1) {
                            imprimir = `<a
                            href="${rotaExport.replace(':id', result.id)}"
                            class="btn btn-sm btn-primary"
                            target="_blank"
                            >
                                <i class="fas fa-print"></i>
                            </a>`;
                        }
                        return `<button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-toggle="modal"
                            data-target="#sinodal-modal"
                            data-id="${result.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${imprimir}`;

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
                {data: 'federacoes'},
                {data: 'locais'},
                {data: 'aci_repassada'},
                {data: 'aci_necessaria'}
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
