<script>
    var data_grafico_barra = @json($dados);
        
    var config_grafico_barra = {
        type: 'bar',
        data: data_grafico_barra,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    };
    
    new Chart(
        document.getElementById("grafico_barra_{{ str_replace(' ', '', $dados['datasets'][0]['label']) }}"),
        config_grafico_barra
    );
</script>