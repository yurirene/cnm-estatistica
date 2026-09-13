    <script>
        const URL = "{{ route('graficos.index') }}";
        const TOKEN = "{{ csrf_token() }}";
        const CORES = {
            umps: '#2E9E8F',
            fed: '#8B2E45',
            sin: '#1B2A63',
            blue: '#2E43A0',
            pale: '#DCE0F5',
            muted: '#767B94',
            line: '#E7E7EE'
        };
        const chartsPorAba = {
            geral: ['chartEvolucao'],
            perfil: ['genero', 'idade', 'estado_civil', 'escolaridade'],
            espiritualidade: ['programacoes'],
            inclusao: ['deficiencias'],
            financeiro: ['repasse_aci']
        };
        let lastResponse = null;
        let abaAtual = 'geral';

        function formatarNumero(valor) {
            const n = Number(valor);
            if (Number.isNaN(n)) {
                return valor;
            }
            return n.toLocaleString('pt-BR');
        }

        function criarOuAtualizarChart(id, config) {
            const el = document.getElementById(id);
            if (!el || !config) {
                return;
            }
            const existente = typeof Chart !== 'undefined' ? Chart.getChart(el) : null;
            if (existente) {
                existente.destroy();
            }
            config.options = config.options || {};
            config.options.maintainAspectRatio = false;
            if (config.percentTicks && config.options.scales && config.options.scales.x) {
                config.options.scales.x.ticks = config.options.scales.x.ticks || {};
                config.options.scales.x.ticks.callback = function (v) {
                    return v + '%';
                };
            }
            new Chart(el, config);
        }

        function criarEvolucao(evolucao) {
            if (!evolucao) {
                return;
            }
            criarOuAtualizarChart('chartEvolucao', {
                type: 'line',
                data: {
                    labels: evolucao.labels,
                    datasets: [
                        {
                            label: 'Taxa de resposta (%)',
                            data: evolucao.resposta,
                            borderColor: CORES.blue,
                            backgroundColor: CORES.blue,
                            tension: 0.3
                        },
                        {
                            label: 'Repasse de ACI (%)',
                            data: evolucao.aci,
                            borderColor: CORES.umps,
                            backgroundColor: CORES.umps,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        function preencherFunil(seletor, passos, opcoes) {
            const $el = $(seletor);
            $el.empty();
            const cores = (opcoes && opcoes.cores) || [CORES.sin, CORES.fed, CORES.umps, CORES.blue];
            (passos || []).forEach(function (passo, index) {
                const largura = Math.max(8, Math.min(100, Number(passo.percentual) || 0));
                const valor = passo.texto || (formatarNumero(passo.valor) + ' · ' + passo.percentual + '%');
                const $step = $('<div class="painel-funnel-step"></div>');
                $step.append($('<div class="painel-funnel-label"></div>').text(passo.label));
                $step.append(
                    $('<div class="painel-funnel-bar"></div>')
                        .css({ width: largura + '%', background: cores[index % cores.length] })
                        .text(passo.percentual + '%')
                );
                $step.append($('<div class="painel-funnel-val"></div>').text(valor));
                $el.append($step);
            });
        }

        function preencherBarras(itens) {
            const $el = $('#socios-barras');
            $el.empty();
            const max = Math.max.apply(null, (itens || []).map(function (i) { return Number(i.valor) || 0; }).concat([1]));
            (itens || []).forEach(function (item) {
                const pct = Math.round((Number(item.valor) || 0) * 100 / max);
                const $row = $('<div class="painel-region-row"></div>');
                $row.append($('<div class="painel-region-name"></div>').text(item.nome));
                $row.append(
                    $('<div class="painel-region-track"></div>').append(
                        $('<div class="painel-region-fill"></div>').css('width', pct + '%')
                    )
                );
                $row.append($('<div class="painel-region-val"></div>').text(formatarNumero(item.valor)));
                $el.append($row);
            });
        }

        function preencherRanking(itens) {
            const $el = $('#ranking-federacoes');
            $el.empty();
            if (!itens || !itens.length) {
                $el.append($('<div class="painel-note"></div>').text('Nenhuma federação com UMPs ativas neste recorte.'));
                return;
            }
            itens.forEach(function (item) {
                const $row = $('<div class="painel-rank-row"></div>');
                const $name = $('<div class="name"></div>');
                $name.append($('<span class="painel-rank-pos"></span>').addClass(item.tipo || '').text(item.pos));
                $name.append(document.createTextNode(' ' + item.nome));
                $row.append($name);
                $row.append(
                    $('<span class="painel-rank-val"></span>')
                        .addClass(item.tipo || '')
                        .text(item.percentual + '%')
                );
                $el.append($row);
            });
        }

        function preencherMedias(medias) {
            const $el = $('#painel-medias');
            $el.empty();
            (medias || []).forEach(function (item) {
                const $col = $('<div class="col-md-4 mt-2"></div>');
                const $card = $('<div class="painel-percapita"></div>');
                $card.append($('<div class="n"></div>').text(item.valor));
                $card.append($('<div class="l"></div>').text(item.label));
                $col.append($card);
                $el.append($col);
            });
        }

        function preencherPainel(response) {
            lastResponse = response;
            const totais = response.totalizadores || {};
            ['total_sinodais', 'total_federacoes', 'total_umps', 'total_socios'].forEach(function (key) {
                $('#' + key).text(formatarNumero(totais[key] ?? '—'));
            });
            ['relatorios_sinodais', 'relatorios_federacoes', 'relatorios_umps_locais', 'qualidade_relatorio'].forEach(function (key) {
                $('#' + key).text(totais[key] ?? '—');
            });

            const visao = response.visao_geral || {};
            const $insights = $('#painel-insights').empty();
            (visao.insights || []).forEach(function (texto) {
                $insights.append($('<li></li>').html(texto));
            });
            $('#socios-barras-titulo').text(visao.socios_barras_titulo || 'Sócios por região');
            preencherBarras(visao.socios_barras);
            preencherFunil('#funil-completude', visao.funil);
            preencherRanking(visao.ranking_federacoes);
            preencherMedias(visao.medias);

            const perfil = response.perfil || {};
            $('#tipo_ativos').text(formatarNumero(perfil.ativos ?? 0));
            $('#tipo_cooperadores').text(formatarNumero(perfil.cooperadores ?? 0));
            $('#tipo_ativos_pct').text((perfil.ativos_pct ?? 0) + '%');
            $('#tipo_cooperadores_pct').text((perfil.cooperadores_pct ?? 0) + '%');

            preencherFunil('#funil-discipulado', (response.espiritualidade || {}).discipulado, {
                cores: [CORES.sin, CORES.blue, CORES.umps, CORES.fed, CORES.sin]
            });

            const categorias = ((response.inclusao || {}).categorias) || [];
            const idsIncl = ['visual', 'auditiva', 'fisica', 'neuro'];
            categorias.forEach(function (cat, i) {
                const id = idsIncl[i];
                if (!id) {
                    return;
                }
                $('#incl_' + id + '_total').text(formatarNumero(cat.total));
                $('#incl_' + id + '_pct').text(cat.percentual + '%');
            });

            const fin = response.financeiro || {};
            ['umps', 'federacoes', 'sinodais'].forEach(function (nivel) {
                const bloco = fin[nivel] || {};
                $('#fin_' + nivel + '_taxa').text((bloco.taxa ?? 0) + '%');
                $('#fin_' + nivel + '_detalhe').text(
                    formatarNumero(bloco.sim ?? 0) + ' repassaram · ' + formatarNumero(bloco.nao ?? 0) + ' não'
                );
            });

            renderGraficosAba(abaAtual);
        }

        function renderGraficosAba(aba) {
            if (!lastResponse) {
                return;
            }
            if (aba === 'geral') {
                criarEvolucao(lastResponse.visao_geral && lastResponse.visao_geral.evolucao);
                const dist = (lastResponse.graficos || []).find(function (g) { return g.id === 'distribuicao'; });
                if (dist) {
                    montarGraficoDistribuicao(dist.dados);
                }
                return;
            }
            (chartsPorAba[aba] || []).forEach(function (id) {
                const grafico = (lastResponse.graficos || []).find(function (g) { return g.id === id; });
                if (grafico && grafico.config) {
                    criarOuAtualizarChart(id, grafico.config);
                }
            });
        }

        function ativarAba(aba) {
            abaAtual = aba;
            $('.painel-estatistica .nav-link').removeClass('active');
            $('.painel-estatistica .nav-link[data-tab="' + aba + '"]').addClass('active');
            $('.painel-tab-pane').removeClass('active');
            $('.painel-tab-pane[data-tab-pane="' + aba + '"]').addClass('active');
            setTimeout(function () {
                renderGraficosAba(aba);
            }, 30);
        }

        $('#filtrar').on('click', function () {
            $('.skeleton-loading').show();
            $.ajax({
                url: URL,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    ano: $('#ano').val(),
                    regiao: $('#regiao').val()
                },
                success: function (response) {
                    $('.skeleton-loading').hide();
                    preencherPainel(response);
                },
                error: function () {
                    $('.skeleton-loading').hide();
                }
            });
        });

        $(document).on('click', '.painel-estatistica .nav-link', function (e) {
            e.preventDefault();
            ativarAba($(this).data('tab'));
        });

        $(document).ready(function () {
            $('#filtrar').click();
        });

        function montarGraficoDistribuicao(dados) {
            if (typeof Highcharts === 'undefined') {
                return;
            }
            var dadosFormatados = dados.map(function (item) {
                return [item['hc-key'], item['n_socios']];
            });
            Highcharts.mapChart('distribuicao', {
                chart: {
                    map: 'countries/br/br-all',
                    height: 480
                },
                colorAxis: {
                    min: 0
                },
                title: {
                    text: ''
                },
                credits: {
                    enabled: false
                },
                navigation: {
                    buttonOptions: {
                        enabled: false
                    }
                },
                series: [{
                    borderColor: '#666',
                    borderWidth: 0.4,
                    data: dadosFormatados,
                    tooltip: {
                        pointFormatter: function () {
                            let point = this;
                            let info = [];
                            dados.forEach(d => {
                                if (d['hc-key'] == point['hc-key']) {
                                    info = [
                                        d.n_socios,
                                        d.n_umps,
                                        d.n_federacoes
                                    ];
                                }
                            });
                            return `${point.name} <br>
                                <b>Nº Sócios</b>: ${info[0]} <br>
                                <b>Nº UMPs Locais</b>: ${info[1]} <br>
                                <b>Nº Federações</b>: ${info[2]}`;
                        }
                    },
                    name: 'Distribuição por Estado',
                    states: {
                        hover: {
                            color: '#BADA55'
                        },
                        select: {
                            color: 'gray'
                        }
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
        }
    </script>
