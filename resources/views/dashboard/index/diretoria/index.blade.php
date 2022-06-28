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
                                <h6 class="text-uppercase text-light ls-1 mb-1">Perfil de Atividades</h6>
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
        <div class="row mt-5">
            <div class="col-xl-6">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Entrega dos Formulários</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <!-- Projects table -->
                            <table class="table align-items-center table-flush" id="tabela-formulario">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Ação</th>
                                        <th scope="col">Sinodal</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(DashboardHelper::getFormularioEntregue() as $formulario)
                                    <tr>
                                        <td scope="row"> 
                                            <button class="btn btn-primary btn-sm btn-info-sinodal" 
                                                data-sinodal='{{ $formulario['id'] }}'>
                                                    <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                        <th scope="row"> {{$formulario['sinodal']}} </th>
                                        <td> {!! $formulario['status'] !!} </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <th scope="row" colspan="2">
                                            Sem Sinodais Cadastradas
                                        </th>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
@endpush

@push('script')
<script>
    const config_grafico_atividade = {
            type: 'radar',
            data: @json(DashboardHelper::getGraficoAtividades()),
            options: {
                elements: {
                    line: {
                        borderWidth: 3
                    }
                }
            },
        };
    const atividadeChart = new Chart(
        document.getElementById('atividades'),
        config_grafico_atividade
    );

    $('#tabela-formulario').dataTable();

    $('.btn-info-sinodal').on('click', function() {
        let rota = '{{ route("dashboard.") }}';
        $.ajax({
            url: rota
        });
    })


</script>
@endpush