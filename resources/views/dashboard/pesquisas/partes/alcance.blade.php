<div class="row">
    <div class="col-md-6">
        @if (isset($alcance['sinodal']))
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-stats shadow mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">Quantidade de Respostas de Sinodais</h5>
                                <span class="h5 font-weight-bold mb-0">
                                    Porcentagem: {{$alcance['sinodal']['porcentagem']}}% <br>                                            
                                    Quantidade: {{$alcance['sinodal']['quantidade']}}
                                </span>
                                
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary" id="filtrar_sinodais">Exibir Mapa</button>
                                <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                    <i class="fas fa-check-double"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(isset($alcance['federacao']))
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-stats shadow mb-4 mb-xl-0 h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">Quantidade de Respostas de Federações</h5>
                                <span class="h5 font-weight-bold mb-0">
                                    Porcentagem: {{ $alcance['federacao']['porcentagem'] }}% <br>                                            
                                    Quantidade: {{ $alcance['federacao']['quantidade'] }}
                                </span>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary" id="filtrar_federacoes">Exibir Mapa</button>
                                <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(isset($alcance['local']))
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-stats shadow mb-4 mb-xl-0 h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">Quantidade de Respostas de UMPs Locais</h5>
                                <span class="h5 font-weight-bold mb-0">
                                    Porcentagem: {{ $alcance['local']['porcentagem'] }}% <br>                                            
                                    Quantidade: {{ $alcance['federacao']['quantidade'] }}
                                </span>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary" id="filtrar_locais">Exibir Mapa</button>
                                <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                    <i class="fas fa-check-square"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-6 mt-3">
        <div id="mapa_formatado_sinodal" style="display: none;"></div>
        <div id="mapa_formatado_federacao" style="display: none;"></div>
        <div id="mapa_formatado_local" style="display: none;"></div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="select_filtro">Filtrar por:</label>
                        <select class="form-control" id="select_filtro">
                            <option value="null" selected disabled>Sem filtro</option>
                            <option value="sinodal">Sinodal</option>
                            <option value="federacao">Federação</option>
                            <option value="local">UMP Local</option>
                        </select>
                    </div>
                    <div class="col">
                        <br>
                        <button class="btn btn-warning mt-2" id="limpar_filtro">Limpar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')

<script>
    const url = "{{ route('dashboard.pesquisas.relatorio', $pesquisa->id) }}";
    $('#select_filtro').on('change', function() {
        if ($(this).val() == null) {
            $(location).prop("href", url);
        } else {
            $(location).prop("href", url + '?filtro=' + $(this).val());
        }
    });
    $('#limpar_filtro').on('click', function() {        
        $(location).prop("href", url);
    });

    $('#filtrar_sinodais').on('click', function() {
        $('#mapa_formatado_sinodal').show();
        $('#mapa_formatado_federacao').hide();
        $('#mapa_formatado_local').hide();
    });
    $('#filtrar_federacoes').on('click', function() {
        $('#mapa_formatado_sinodal').hide();
        $('#mapa_formatado_federacao').show();
        $('#mapa_formatado_local').hide();
    });
    $('#filtrar_locais').on('click', function() {
        $('#mapa_formatado_sinodal').hide();
        $('#mapa_formatado_federacao').hide();
        $('#mapa_formatado_local').show();
    });
</script>
@if (isset($mapa_alcance['sinodal']))
<script>
    
    var regiao_data = @json($mapa_alcance['sinodal']);
    var mapData = Highcharts.maps['countries/br/br-all'];
	var data = mapData.features.map(function (feature) {
        return {
            'hc-key': feature.properties['hc-key'],
            value: regiao_data[feature.properties['region']] || null
        }
    });
    
    Highcharts.mapChart('mapa_formatado_sinodal', {
        chart: {
            map: mapData
        },

        title: {
            text: 'Quantidade de Sinodais que Responderam'
        },

        mapNavigation: {
            enabled: true,
            enableDoubleClickZoomTo: true
        },

        colorAxis: {
         enabled: true
        },

        tooltip: {
            pointFormat: 'Região {point.properties.region}: <b>{point.value}% das Sinodais</b>'
        },

        series: [{
            data: data,
            joinBy: 'hc-key',
            name: 'Respostas'
        }]
    });

</script>
@endif
@if (isset($mapa_alcance['federacao']))
<script>
    Highcharts.mapChart('mapa_formatado_federacao', {
        chart: {
            map: 'countries/br/br-all',
        },

        title: {
            text: 'Quantidade de Federações que Responderam'
        },

        credits: {
            enabled: false
        },
        mapNavigation: {
            enabled: true,
            enableDoubleClickZoomTo: true
        },

        colorAxis: {
         enabled: true
        },

        tooltip: {
            pointFormat: 'Estado {point.properties.name}: <b>{point.value}% das Federações</b>'
        },

        series: [{
            data: @json($mapa_alcance['federacao']),
            joinBy: 'hc-key',
            name: 'Respostas',
            dataLabels: {
                enabled: true,
                format: '{point.name}',
                style: {
                    fontSize: '7px',
                    textOutline: '0px',
                    fontWeight: 'normal'
                },
            },
        }]
    });
</script>
@endif
@if (isset($mapa_alcance['local']))
<script>
    Highcharts.mapChart('mapa_formatado_local', {
        chart: {
            map: 'countries/br/br-all',
        },

        title: {
            text: 'Quantidade de UMPs Locais que Responderam'
        },

        credits: {
            enabled: false
        },
        mapNavigation: {
            enabled: true,
            enableDoubleClickZoomTo: true
        },

        colorAxis: {
         enabled: true
        },

        tooltip: {
            pointFormat: 'Estado {point.properties.name}: <b>{point.value}% das UMPs Locais</b>'
        },

        series: [{
            data: @json($mapa_alcance['local']),
            joinBy: 'hc-key',
            name: 'Respostas',
            dataLabels: {
                enabled: true,
                format: '{point.name}',
                style: {
                    fontSize: '7px',
                    textOutline: '0px',
                    fontWeight: 'normal'
                },
            },
        }]
    });
</script>
@endif
@endpush