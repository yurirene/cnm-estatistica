@php
    $execPresidente = $execPresidente ?? false;
    $hero = DashboardHelper::getDashboardHero();
    $qualidade = DashboardHelper::getQualidadeEntregaRelatorios();
    $sociosSerie = $hero['socios']['serie'] ?? [0, 0, 0, 0];
    $mapaDadosBrasil = $dataMapaBrazil ?? [];
@endphp
@push('js')
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
    <script>
        const mapaDadosBrasil = @json($mapaDadosBrasil);
        const sociosSerie = @json($sociosSerie);
        const qualidadeData = @json($qualidade);

        $(document).on('click', '.exec-hint', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $this = $(this);
            $('.exec-hint').not($this).removeClass('is-open');
            $this.toggleClass('is-open');
        });
        $(document).on('click', function (e) {
            if ($(e.target).closest('.exec-hint').length) {
                return;
            }
            $('.exec-hint').removeClass('is-open');
        });

        const miniTrendEl = document.getElementById('chartMiniTrend');
        if (miniTrendEl) {
            new Chart(miniTrendEl, {
                type: 'line',
                data: {
                    labels: ['', '', '', ''],
                    datasets: [{
                        data: sociosSerie,
                        borderColor: '#2E43A0',
                        backgroundColor: 'rgba(46,67,160,0.08)',
                        fill: true,
                        lineTension: 0.35,
                        pointRadius: 0,
                        borderWidth: 2
                    }]
                },
                options: {
                    legend: { display: false },
                    scales: {
                        xAxes: [{ display: false }],
                        yAxes: [{ display: false }]
                    },
                    maintainAspectRatio: false
                }
            });
        }

        const entregasEl = document.getElementById('entregas');
        if (entregasEl) {
            new Chart(entregasEl, {
                type: 'doughnut',
                data: {
                    labels: qualidadeData.labels,
                    datasets: qualidadeData.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 70,
                    legend: { display: false }
                }
            });
        }

        const mapaMetricas = {
            n_socios: { titulo: 'Nº de Sócios', sub: 'Intensidade da cor = número de sócios por estado.', suffix: '' },
            taxa_resposta: { titulo: 'Taxa de resposta (%)', sub: 'Intensidade da cor = percentual de UMPs locais que entregaram o formulário.', suffix: '%' },
            aci: { titulo: 'ACI repassada', sub: 'Intensidade da cor = valor de ACI informado no estado.', suffix: '' }
        };

        function pontosMapa(metric) {
            return mapaDadosBrasil.map(function (item) {
                return {
                    'hc-key': item['hc-key'],
                    value: item[metric] || 0,
                    n_socios: item.n_socios || 0,
                    n_umps: item.n_umps || 0,
                    n_federacoes: item.n_federacoes || 0,
                    taxa_resposta: item.taxa_resposta || 0,
                    aci: item.aci || 0
                };
            });
        }

        let mapaChart = null;
        if (document.getElementById('mapa-brazil')) {
            mapaChart = Highcharts.mapChart('mapa-brazil', {
                chart: {
                    map: 'countries/br/br-all',
                    height: 420
                },
                title: { text: '' },
                credits: { enabled: false },
                navigation: { buttonOptions: { enabled: false } },
                colorAxis: {
                    min: 0,
                    minColor: '#E8F4FD',
                    maxColor: '#1565C0'
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                series: [{
                    borderColor: '#666',
                    borderWidth: 0.4,
                    data: pontosMapa('n_socios'),
                    joinBy: 'hc-key',
                    tooltip: {
                        pointFormatter: function () {
                            return '<b>' + this.name + '</b><br/>' +
                                'Nº de Sócios: <b>' + Highcharts.numberFormat(this.n_socios, 0) + '</b><br/>' +
                                'Nº de UMPs: <b>' + Highcharts.numberFormat(this.n_umps, 0) + '</b><br/>' +
                                'Nº de Federações: <b>' + Highcharts.numberFormat(this.n_federacoes, 0) + '</b><br/>' +
                                'Taxa de resposta: <b>' + Highcharts.numberFormat(this.taxa_resposta, 1) + '%</b><br/>' +
                                'ACI: <b>R$ ' + Highcharts.numberFormat(this.aci, 2, ',', '.') + '</b>';
                        }
                    },
                    name: 'Nº de Sócios',
                    states: {
                        hover: { brightness: 0.15 },
                        select: { color: '#455A64' }
                    },
                    allowPointSelect: true,
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}',
                        style: {
                            fontSize: '7px',
                            textOutline: '0px',
                            fontWeight: 'normal'
                        }
                    }
                }]
            });

            $('#mapa-toggles button').on('click', function () {
                const metric = $(this).data('metric');
                $('#mapa-toggles button').removeClass('active');
                $(this).addClass('active');
                $('#mapa-subtitulo').text(mapaMetricas[metric].sub);
                mapaChart.series[0].setData(pontosMapa(metric), true);
                mapaChart.series[0].update({ name: mapaMetricas[metric].titulo });
            });
        }
    </script>
@endpush

@push('js')
<script>
    function getAnoReferenciaFiltro() {
        return $('#filtro-ano-referencia').val();
    }

    function getRegiaoFiltro() {
        return $('#filtro-regiao').val() || '';
    }

    function urlComAnoReferencia(url) {
        var separator = url.indexOf('?') === -1 ? '?' : '&';
        return url + separator + 'ano_referencia=' + encodeURIComponent(getAnoReferenciaFiltro());
    }

    function statusChip(status) {
        var labels = { completo: 'Completo', parcial: 'Parcial', pendente: 'Pendente' };
        var classe = status || 'pendente';
        return `<span class="status-chip ${classe}">${labels[classe] || 'Pendente'}</span>`;
    }

    $(function() {
        var rotaExport = "{{ route('dashboard.formularios-sinodal.export', ':id') }}";
        var isPresidente = {{ $execPresidente ? 'true' : 'false' }};
        var columns = [
            {
                render: function (data, type, result) {
                    var imprimir = '';
                    if (result.entregue == 1) {
                        imprimir = `<a
                            href="${rotaExport.replace(':id', result.id)}?ano_referencia=${getAnoReferenciaFiltro()}"
                            class="btn btn-sm btn-primary"
                            target="_blank"
                        >
                            <i class="fas fa-print"></i>
                        </a>`;
                    }
                    return `<button
                        type="button"
                        class="btn btn-sm btn-primary"
                        data-toggle="modal"
                        data-target="#sinodal-modal"
                        data-id="${result.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${imprimir}`;
                }
            },
            {data: 'nome'},
            {
                render: function (data, type, result) {
                    return statusChip(result.status);
                }
            },
            {data: 'federacoes'},
            {data: 'locais'},
            {data: 'aci_repassada'},
            {data: 'aci_necessaria'}
        ];

        if (isPresidente) {
            columns.push({data: 'progresso'});
            columns.push({data: 'regiao'});
        }

        columns.push({
            render: function (data, type, result) {
                if (!result.whatsapp || result.status === 'completo') {
                    return '';
                }
                return `<a class="row-action" href="${result.whatsapp}" target="_blank" rel="noopener noreferrer">Lembrar</a>`;
            }
        });

        $('#sinodal-entregues-table').DataTable({
            dom: 'frtipl',
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            createdRow: function (row, data) {
                if (data.zero_resposta) {
                    $(row).addClass('priority');
                }
            },
            ajax: {
                url: '{{ route("dashboard.datatables.formularios-entregues", "Sinodal") }}',
                data: function (d) {
                    d.ano_referencia = getAnoReferenciaFiltro();
                    if (isPresidente) {
                        d.regiao_id = getRegiaoFiltro();
                    }
                }
            },
            columns: columns
        });

        $('#filtro-ano-referencia, #filtro-regiao').on('change', function () {
            $('#sinodal-entregues-table').DataTable().ajax.reload();
        });
    });

    $('#sinodal-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var route = urlComAnoReferencia(
            '{{ route("dashboard.datatables.formularios-entregues", ["instancia" => "Federacao", "id" => ":id"]) }}'.replace(':id', id)
        );
        carregarDataTableFederacao(route);
    });

    $('#local-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var route = urlComAnoReferencia(
            '{{ route("dashboard.datatables.formularios-entregues", ["instancia" => "Local", "id" => ":id"]) }}'.replace(':id', id)
        );
        carregarDataTableLocal(route);
    });

    function carregarDataTableFederacao(route) {
        $('#federacao-entregues-table').DataTable().destroy();
        $('#federacao-entregues-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: route,
            columns: [
                {
                    render: function (data, type, result) {
                        return `<button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-toggle="modal"
                            data-target="#local-modal"
                            data-id="${result.id}">
                                <i class="fas fa-eye"></i>
                            </button>`;
                    }
                },
                {data: 'nome'},
                {
                    render: function (data, type, result) {
                        return `<span class="badge bg-${result.entregue == 1 ? 'success' : 'danger'}">
                            ${result.entregue == 1 ? 'Entregue' : 'Pendente'}
                        </span>`;
                    }
                },
            ]
        });
    }

    function carregarDataTableLocal(route) {
        $('#local-entregues-table').DataTable().destroy();
        $('#local-entregues-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: route,
            columns: [
                {data: 'nome'},
                {
                    render: function (data, type, result) {
                        return `<span class="badge bg-${result.entregue == 1 ? 'success' : 'danger'}">
                            ${result.entregue == 1 ? 'Entregue' : 'Pendente'}
                        </span>`;
                    }
                },
            ]
        });
    }
</script>
@endpush
