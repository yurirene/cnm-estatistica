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
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Gráfico de Entrega de Relatórios por Região</h6>
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
        <div class="modal fade" id="log-erro-modal" tabindex="-1" role="dialog" aria-labelledby="log-erro-modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="log-erro-modalLabel">Erro do Sistema</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>Data: <span  id="dia_modal"></span></h4>
                    <h5>Erro: <span  id="erro_modal"></span></h5>
                    <h5>Linha: <span  id="linha_modal"></span></h5>
                    <h5>Arquivo: <span  id="arquivo_modal"></span></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
                </div>
            </div>
        </div>
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
            order: [1],
            ajax: '{{ route("dashboard.datatables.log-erros") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        return `<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#log-erro-modal" data-dia="${result.dia}" data-informacoes="${result.erro_completo}"><i class="fas fa-eye"></i></button>`;
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

    $('#log-erro-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var informacoes = button.data('informacoes')
        var dia = button.data('dia')
        var modal = $(this)
        modal.find('#dia_modal').text(dia);
        modal.find('#erro_modal').text(informacoes.message);
        modal.find('#linha_modal').text(informacoes.line);
        modal.find('#arquivo_modal').text(informacoes.file);
    })


</script>
@endpush
