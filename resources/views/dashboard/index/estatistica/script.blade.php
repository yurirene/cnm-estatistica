
    <script>
        const URL = "{{ route('graficos.index') }}";
        const TOKEN = "{{ csrf_token() }}";
        const LEGEND = {
            legend: {
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
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const labelIndex = (context.datasetIndex * 2) + context.dataIndex;
                        return context.chart.data.labels[labelIndex] + ': ' + context.formattedValue;
                    }
                }
            }
        };

        $('#filtrar').on('click', function () {
            $('.skeleton-loading').show();
            $.ajax({
                url: URL,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    ano: $('#ano').val(),
                    regiao: $('#regiao').val()
                },
                success: function(response) {
                    $('.skeleton-loading').hide();
                    response.graficos.forEach(function(grafico) {
                        let chart = Chart.getChart(grafico.id);
                        if (chart) {
                            chart.destroy();
                        }
                        if (grafico.config.need) {
                            grafico.config.options.plugins = LEGEND;
                        }

                        if (grafico.id == 'distribuicao') {
                            montarGraficoDistribuicao(grafico.dados);
                            return;
                        }

                        new Chart(
                            document.getElementById(grafico.id),
                            grafico.config
                        );
                    });
                    let keys = Object.keys(response.totalizadores);
                    keys.forEach(function(key) {
                        $(`#${key}`).text(response.totalizadores[key]);
                    });
                }
            });
        });
        $(document).ready(function() {
            $('#filtrar').click();
        })

        function montarGraficoDistribuicao(dados) {
            var dadosFormatados = dados.map(function(item) {
                return [item['hc-key'], item['n_socios']];
            });
            Highcharts.mapChart('distribuicao', {
                chart: {
                    map: 'countries/br/br-all',
                    height: 600
                },

                colorAxis: {
                    min: 0
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
                    data: dadosFormatados,
                    tooltip: {
                        pointFormatter: function() {
                            let point = this;
                            let info = [];

                            dados.forEach(d => {
                                if(d['hc-key'] == point['hc-key']){
                                    info = [
                                        d.n_socios,
                                        d.n_umps,
                                        d.n_federacoes
                                    ]
                                }
                            });
                            return `${point.name} <br>
                                <b>Nº Sócios</b>: ${info[0]} <br>
                                <b>Nº UMPs Locais</b>: ${info[1]} <br>
                                <b>Nº Federações</b>: ${info[2]}`

                        }
                    },
                    name: 'Distribuição por Estado',
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
        }
    </script>
