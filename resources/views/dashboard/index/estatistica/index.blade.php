@extends('layouts.app')

@section('content')
    @include('dashboard.index.estatistica.cards', [
        'totalizador' => DashboardHelper::getTotalizadores()
    ])

    <div class="container-fluid mt--7">
        <div class="row mb-5">
            <div class="col-xl-12 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="text-uppercase text-light ls-1 mb-1">Parâmetros</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ano</label>
                                    <select class="form-control" id="ano">
                                        <option value="2022">2022</option>
                                    <select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Região</label>
                                    <select class="form-control" id="regiao">
                                        <option value="">Todas</option>
                                        <option value="1">Norte</option>
                                        <option value="2">Nordeste</option>
                                        <option value="3-oeste">Centro-Oeste</option>
                                        <option value="4">Sudeste</option>
                                        <option value="5">Sul</option>
                                    <select>
                                </div>
                            </div>
                            <div class="col-md-3 mt-1">
                                <button class="btn btn-default" typle="button" id="filtrar">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Tipo de Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <canvas id="tipo_socios"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Gênero dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <canvas id="genero"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-6 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Idade dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <canvas id="idade"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">

            <div class="mt-3 col-xl-6 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Estado Civil dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <canvas id="estado_civil"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Escolaridade dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <canvas id="escolaridade"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Sócios Desempregados</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        <div class="table-responsive">
                            <canvas id="desempregados"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">

            <div class="mt-3 col-xl-8 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Sócios com Deficiências</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <canvas id="deficiencias"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-4 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Repasse da ACI</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <canvas id="repasse_aci"></canvas>
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
        const URL = "{{ route('graficos.index') }}";
        const TOKEN = "{{ csrf_token() }}";
        const LEGEND = {
            labels: {
                generateLabels: function(chart) {
                    const original = Chart.overrides.pie.plugins.legend.labels.generateLabels;
                    const labelsOriginal = original.call(this, chart);
                    let datasetColors = chart.data.datasets.map(function(e) {
                        return e.backgroundColor;
                    });
                    datasetColors = datasetColors.flat();
                    labelsOriginal.forEach(label => {
                        label.datasetIndex = (label.index - label.index % 2) / 2;
                        label.hidden = !chart.isDatasetVisible(label.datasetIndex);
                        label.fillStyle = datasetColors[label.index];
                    });
                    return labelsOriginal;
                }
            },
            onClick: function(mouseEvent, legendItem, legend) {
                legend.chart.getDatasetMeta(
                    legendItem.datasetIndex
                ).hidden = legend.chart.isDatasetVisible(legendItem.datasetIndex);
                legend.chart.update();
            }
        };
        $('#filtrar').on('click', function () {
            $.ajax({
                url: URL,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    ano: $('#ano').val(),
                    regiao: $('#regiao').val()
                },
                success: function(response) {
                    response.forEach(function(grafico) {
                        let chart = Chart.getChart(grafico.id);
                        if (chart) {
                            chart.destroy();
                        }
                        if (grafico.config.need) {
                            grafico.config.options.plugins.legend = LEGEND;
                        }
                        new Chart(
                            document.getElementById(grafico.id),
                            grafico.config
                        );
                    })
                }
            });
        });
    </script>

@endpush
