<div class="card shadow">
    <div class="card-header">
        <h3>Escolaridade</h3>
    </div>
    <div class="card-body">
        <canvas id="escolaridade"></canvas>        
    </div>
</div>

@push('script')

<script>
    function montarGraficoEscolaridade(data)
    {
        const config_grafico_escolaridade = {
            type: 'doughnut',
            data: data,
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%'
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        };
        const escolaridadeChart = new Chart(
        document.getElementById('escolaridade'),
        config_grafico_escolaridade
        );
    }
    
</script>

@endpush