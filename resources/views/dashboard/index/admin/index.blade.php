@extends('layouts.app')

@section('content')
    @include('dashboard.index.admin.cards', [
        'totalizador' => DashboardHelper::getTotalizadores()
    ])
    
    <div class="container-fluid mt--7">

        <div class="row">
            <div class="col-xl-7 mb-5 mb-xl-0">
                <div class="card bg-gradient-default shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-light ls-1 mb-1">Acesso na Plataforma</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart -->
                        <div class="">
                            <!-- Chart wrapper -->
                            <canvas id="atividades"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Logs de Errros</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="log-erros-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Erro</th>
                                        <th class="text-center">Usuário Afetado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-6">
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
                            <canvas id="grafico-formularios-entregues"></canvas>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
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
        @include('layouts.footers.auth')
    </div>
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
    
<script>
    $(function() {
        $('#log-erros-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.log-erros") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        return `<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#log-erro-modal" data-idlog="${result.id}"><i class="fas fa-eye"></i></button>`;
                    }
                },
                {data: 'dia'},
                {data: 'erro'},
                {data: 'usuario'},
            ]
        });
    });
</script>
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
                },
            }]
        });
    </script>
    <script>
        var config_grafico_entrega_relatorio = {
            type: 'polarArea',
            data: @json(DashboardHelper::getTotalizadores()['grafico_entrega_formulario_por_regiao']),
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                console.log(context);
                                return context.label + ': ' + context.parsed.r + '%'
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scale: {
                    ticks: {
                        min: 0,
                        max: 100
                    }
                }
            }
        };
        const programcaoChart = new Chart(
            document.getElementById('grafico-formularios-entregues'),
            config_grafico_entrega_relatorio
        );
    </script>
@endpush

@push('script')
<script>
    const config_grafico_acesso = {
            type: 'line',
            data: @json(DashboardHelper::getTotalizadores()['grafico_acesso_trinta_dias']),
            options: {
                elements: {
                    line: {
                        borderWidth: 3
                    }
                },
                responsive: true,
                    plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Gráfico de Acessos'
                    }
                }
            },
        };
    const acessoChart = new Chart(
        document.getElementById('atividades'),
        config_grafico_acesso
    );

    // $('#tabela-formulario').dataTable();

    // $('.btn-info-sinodal').on('click', function() {
    //     let rota = '{{ route("dashboard.usuarios.index") }}';
    //     $.ajax({
    //         url: rota
    //     });
    // })


</script>
@endpush