<script>
    var data_grafico_linha = @json($dados);
        
    var config_grafico_linha = {
        type: 'line',
        data: data_grafico_linha,
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
        document.getElementById("grafico_linha_{{ str_replace(' ', '', $dados['datasets'][0]['label']) }}"),
        config_grafico_linha
    );
</script>