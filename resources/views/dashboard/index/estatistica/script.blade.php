
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
                        new Chart(
                            document.getElementById(grafico.id),
                            grafico.config
                        );
                    });
                    let keys = Object.keys(response.totalizadores);
                    console.log(keys);
                    keys.forEach(function(key) {
                        $(`#${key}`).text(response.totalizadores[key]);
                    });
                }
            });
        });
        $(document).ready(function() {
            $('#filtrar').click();
        })
    </script>
