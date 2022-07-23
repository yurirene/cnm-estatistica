<script>
    var data_porlar_chart = @json($dados);
    var config_grafico_programacao = {
        type: 'polarArea',
        data: data_porlar_chart,
        options: {
            responsive: true,
            plugins: {
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
                            size: 10
                        }
                    }
                }
            },
        }
    };
    new Chart(
        document.getElementById("grafico_polar_{{ str_replace(' ', '', $dados['datasets'][0]['label']) }}"),
        config_grafico_programacao
    );
    
</script>