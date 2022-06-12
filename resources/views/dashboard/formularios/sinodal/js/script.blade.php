<script>
    $('#responder').on('click', function() {
        $('#formulario_ump').show();
        $('#formulario_importar').hide();
        $('#resumo-card').hide();
    });

    $('#importar').on('click', function() {
        $('#formulario_importar').show();
        $('#formulario_ump').hide();
        $('#resumo-card').hide();
        

    });

    $('#botao-validar').on('click', function() {
        var formData = new FormData(); 
        let token = '{{ csrf_token() }}';

        formData.append("planilha", planilha.files[0]);
        formData.append("_token", token);
        $.ajax({
            type: "POST",
            url: '{{ route("dashboard.formularios-sinodais.importar-validar") }}',
            data: formData,
            contentType : false,
            processData : false,
            success: function(response){
                var html = '';
                response.forEach((item) => {
                    html += `<div class="form-row align-items-center">
                                <div class="col-md-3 my-1">
                                <label class="mr-sm-2" for="federacoes[${item.id_planilha}][federacao_id]">Federação</label>
                                <select class="isSelectFederacoes form-control mr-sm-2" id="federacoes[${item.id_planilha}][federacao_id]" required name="federacoes[${item.id_planilha}][federacao_id]">
                                </select>
                                </div>
                                <div class="col-md-3 my-1">
                                    <label for="federacoes[${item.id_planilha}][presbiterio]">Presbitério</label>
                                    <input type="text" class="form-control mr-sm-2" name="federacoes[${item.id_planilha}][presbiterio]" value="${item.presbiterio}" id="federacoes[${item.id_planilha}][presbiterio]" readonly>
                                </div>
                            </div>`;
                });
                $('#campos-federacoes').html(html);
                inicializarSelect();
                $('#botao-validar').hide();
                $('#botao-importar-enviar').show();
            },
            error: function(error) {
                console.log(error);
                let erros = error.responseJSON.split(';');
                erros.forEach((item) => {
                    if (item.length > 0) {
                        iziToast.error({
                            title: 'Erro!',
                            message: item,
                            position: 'topRight',
                            timeout: 7000
                        });
                    }
                });
            },
            complete: function (data) {
                console.log(data);
            }
            
        });
    });

    function inicializarSelect()
    {
        $('.isSelectFederacoes').select2({
            ajax: {
                url: '{{ route("dashboard.formularios-sinodais.get-federacoes") }}',
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
            }
        });
    }
    
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