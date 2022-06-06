<div class="card shadow">
    <div class="card-header">
        <h3>Tipo de Programação</h3>
    </div>
    <div class="card-body">
        <canvas id="programacoes"></canvas>        
    </div>
</div>
@push('script')
<script>
    function montarGraficoProgramacao(data) {
        var data_programacao = data
        var config_grafico_programacao = {
            type: 'polarArea',
            data: data_programacao,
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.r + '%'
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    r: {
                        pointLabels: {
                            display: true,
                            centerPointLabels: true,
                            font: {
                                size: 18
                            }
                        }
                    }
                },
            }
        };
        const programcaoChart = new Chart(
        document.getElementById('programacoes'),
        config_grafico_programacao
        );
    }
    
    

</script>

@endpush