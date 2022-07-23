<script>
    var data_grafico_pizza = @json($dados);
        
    var config_grafico_pizza = {
        type: 'pie',
        data: data_grafico_pizza,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                
            }
        },
    };
    
    new Chart(
        document.getElementById("grafico_pizza_{{ str_replace(' ', '', $dados['datasets'][0]['label']) }}"),
        config_grafico_pizza
    );
</script>