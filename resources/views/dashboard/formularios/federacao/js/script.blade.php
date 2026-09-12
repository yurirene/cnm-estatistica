<script>


    $(document).ready(function() {
        let link = "{{ route('dashboard.formularios-federacoes.export', ':id') }}";
        let val = link.replace(':id', $('#ano option:selected').text());
        $('#link_export').attr('href', val);


        Number.prototype.formatMoney = function(places, symbol, thousand, decimal) {
            places = !isNaN(places = Math.abs(places)) ? places : 2;
            symbol = symbol !== undefined ? symbol : "$";
            thousand = thousand || ",";
            decimal = decimal || ".";
            var number = this,
                negative = number < 0 ? "-" : "",
                i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;
            return symbol
                + negative
                + (j ? i.substr(0, j)
                + thousand : "")
                + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand)
                + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
        };
    })
    $('#responder').on('click', function() {
        $('#formulario_ump').show();
        $('#resumo-card').hide();
    });
    $('#ano').on('change', function() {
        let link = "{{ route('dashboard.formularios-federacoes.export', ':id') }}";
        let val = link.replace(':id', $('#ano option:selected').text());
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
                $('#aci-recebida').text(json.data.aci.formatMoney(2, "", ".", ","))
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
                $('#resumo-trilha_cnm').text(json.data.discipulado?.trilha_cnm ?? 0)
                $('#resumo-discipulando_cnm').text(json.data.discipulado?.discipulando_cnm ?? 0)
                $('#resumo-discipulando_outro').text(json.data.discipulado?.discipulando_outro ?? 0)
                $('#resumo-sendo_discipulados').text(json.data.discipulado?.sendo_discipulados ?? 0)
                if (typeof feRenderTotalizadorGraficos === 'function') {
                    feRenderTotalizadorGraficos(json.data);
                }
            },
        });
        $('#formulario_ump').show();
    });
    $('#visualizar').on('click', function() {
        $('#formulario_ump').hide();
        $.ajax({
            type: "POST",
            url: '{{ route("dashboard.formularios-federacoes.view") }}',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('#ano').val()
            },
            success: function(json) {
                feRenderResumo(json.data.resumo);
            },
        });
    });

    const ROUTE_APENAS_SALVAR = "{{ route('dashboard.formularios-federacoes.apenas-salvar') }}"
    $('#apenas-salvar').on('click', function () {
        let form = $(this).parents('form:first');
        $(form).attr('action', ROUTE_APENAS_SALVAR);
        $(form).submit();
    })
</script>
