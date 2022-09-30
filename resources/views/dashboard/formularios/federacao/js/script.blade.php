<script>


    $(document).ready(function() {
        let link = "{{ route('dashboard.formularios-federacoes.export', ':id') }}";
        let val = link.replace(':id', $('#ano').text());
        $('#link_export').attr('href', val);
    })
    $('#responder').on('click', function() {
        $('#formulario_ump').show();
        $('#resumo-card').hide();
    });
    $('#ano').on('change', function() {
        let link = "{{ route('dashboard.formularios-locais.export', ':id') }}";
        let val = link.replace(':id', $('#ano').text());
        $('#link_export').attr('href', val);
    })


    $('#responder').on('click', function() {
        $.ajax({
            type: "POST",
            url: '{{ route("dashboard.formularios-federacoes.resumo") }}',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('#federacao_id').val()
            },
            success: function(json) {
                $('#aci-recebida').text(json.data.aci)
                $('#resumo-ativos').text(json.data.perfil.ativos)
                $('#resumo-cooperadores').text(json.data.perfil.cooperadores)
                $('#resumo-homens').text(json.data.perfil.homens)
                $('#resumo-mulheres').text(json.data.perfil.mulheres)
                $('#resumo-menor19').text(json.data.perfil.menor19)
                $('#resumo-de19a23').text(json.data.perfil.de19a23)
                $('#resumo-de24a29').text(json.data.perfil.de24a29)
                $('#resumo-de30a35').text(json.data.perfil.de30a35)
                $('#resumo-fundamental').text(json.data.escolaridade.fundamental)
                $('#resumo-medio').text(json.data.escolaridade.medio)
                $('#resumo-tecnico').text(json.data.escolaridade.tecnico)
                $('#resumo-superior').text(json.data.escolaridade.superior)
                $('#resumo-pos').text(json.data.escolaridade.pos)
                $('#resumo-desempregado').text(json.data.escolaridade.desempregado)
                $('#resumo-solteiros').text(json.data.estado_civil.solteiros)
                $('#resumo-casados').text(json.data.estado_civil.casados)
                $('#resumo-divorciados').text(json.data.estado_civil.divorciados)
                $('#resumo-viuvos').text(json.data.estado_civil.viuvos)
                $('#resumo-filhos').text(json.data.estado_civil.filhos)
                $('#resumo-surdos').text(json.data.deficiencias.surdos)
                $('#resumo-auditiva').text(json.data.deficiencias.auditiva)
                $('#resumo-cegos').text(json.data.deficiencias.cegos)
                $('#resumo-baixa_visao').text(json.data.deficiencias.baixa_visao)
                $('#resumo-fisica_inferior').text(json.data.deficiencias.fisica_inferior)
                $('#resumo-fisica_superior').text(json.data.deficiencias.fisica_superior)
                $('#resumo-neurologico').text(json.data.deficiencias.neurologico)
                $('#resumo-intelectual').text(json.data.deficiencias.intelectual)
            },
        });
        $('#formulario_ump').show();
    });
    $('#visualizar').on('click', function() {
        $.ajax({
            type: "POST",
            url: '{{ route("dashboard.formularios-federacoes.view") }}',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('#ano').val()
            },
            success: function(json) {
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