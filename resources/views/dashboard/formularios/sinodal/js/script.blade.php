<script>
    $('#responder').on('click', function() {
        $('#formulario_ump').show();
    });

    $('#importar').on('click', function() {
        $('#formulario_importar').show();
    });
    
    $('#visualizar').on('click', function() {
        $.ajax({
            type: "POST",
            url: '{{ route("dashboard.formularios-sinodais.view") }}',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('#ano').val()
            },
            success: function(json) {
                console.log(json);
                $('#ano_referencia').text(json.data.resumo.ano_referencia)
                $('#aci').text(json.data.resumo.aci)
                $('#ativos').text(json.data.resumo.ativos)
                $('#cooperadores').text(json.data.resumo.cooperadores)
                $('#homens').text(json.data.resumo.homens)
                $('#mulheres').text(json.data.resumo.mulheres)
                $('#menor19').text(json.data.resumo.menor19)
                $('#de19a23').text(json.data.resumo.de19a23)
                $('#de24a29').text(json.data.resumo.de24a29)
                $('#de30a35').text(json.data.resumo.de30a35)
                $('#fundamental').text(json.data.resumo.fundamental)
                $('#medio').text(json.data.resumo.medio)
                $('#tecnico').text(json.data.resumo.tecnico)
                $('#superior').text(json.data.resumo.superior)
                $('#pos').text(json.data.resumo.pos)
                $('#desempregado').text(json.data.resumo.desempregado)
                $('#solteiros').text(json.data.resumo.solteiros)
                $('#casados').text(json.data.resumo.casados)
                $('#divorciados').text(json.data.resumo.divorciados)
                $('#viuvos').text(json.data.resumo.viuvos)
                $('#filhos').text(json.data.resumo.filhos)
                $('#surdos').text(json.data.resumo.surdos)
                $('#auditiva').text(json.data.resumo.auditiva)
                $('#cegos').text(json.data.resumo.cegos)
                $('#baixa_visao').text(json.data.resumo.baixa_visao)
                $('#fisica_inferior').text(json.data.resumo.fisica_inferior)
                $('#fisica_superior').text(json.data.resumo.fisica_superior)
                $('#neurologico').text(json.data.resumo.neurologico)
                $('#intelectual').text(json.data.resumo.intelectual)
                $('#social').text(json.data.resumo.social)
                $('#evangelistico').text(json.data.resumo.evangelistico)
                $('#espiritual').text(json.data.resumo.espiritual)
                $('#recreativo').text(json.data.resumo.recreativo)
                $('#oracao').text(json.data.resumo.oracao)

                Chart.helpers.each(Chart.instances, function(instance){
                    instance.destroy();
                });
                montarGraficoPerfil(json.data.grafico.perfil);
                montarGraficoProgramacao(json.data.grafico.programacoes);
                montarGraficoEscolaridade(json.data.grafico.escolaridade);

                $('#resumo-card').show();
            },
        });
    });
</script>