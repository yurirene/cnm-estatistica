<script>
(function($) {
    window.feResumoCharts = window.feResumoCharts || [];

    function feInt(value) {
        return parseInt(value, 10) || 0;
    }

    function feMoney(value) {
        var number = parseFloat(value);
        if (isNaN(number)) {
            number = 0;
        }
        if (typeof number.formatMoney === 'function') {
            return number.formatMoney(2, '', '.', ',');
        }
        return number.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function feSetText(id, value) {
        var $el = $('#' + id);
        if (!$el.length) {
            return;
        }
        $el.text(value);
        var $row = $el.closest('.fe-mini-row');
        if ($row.length) {
            $row.toggleClass('is-zero', feInt(value) === 0);
        }
    }

    function feChartId(prefix, name) {
        return prefix ? 'chart-' + prefix + '-' + name : 'chart-' + name;
    }

    function feDestroyChartStore(storeName) {
        var store = window[storeName] || [];
        store.forEach(function(chart) {
            try { chart.destroy(); } catch (e) {}
        });
        window[storeName] = [];
    }

    function feDestroyResumoCharts() {
        feDestroyChartStore('feResumoCharts');
    }

    function feChartDefaults() {
        return {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#767B94', padding: 12, font: { size: 11 } }
                }
            }
        };
    }

    function feMontarGrafico(id, config, storeName) {
        var canvas = document.getElementById(id);
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }
        storeName = storeName || 'feResumoCharts';
        window[storeName] = window[storeName] || [];
        window[storeName].push(new Chart(canvas, config));
    }

    window.feResumoFromTotalizador = function(data) {
        data = data || {};
        var perfil = data.perfil || {};
        var escolaridade = data.escolaridade || {};
        var discipulado = data.discipulado || {};
        var programacoes = data.programacoes_locais || data.programacoes || {};
        return {
            ativos: perfil.ativos,
            cooperadores: perfil.cooperadores,
            homens: perfil.homens,
            mulheres: perfil.mulheres,
            menor19: perfil.menor19,
            de19a23: perfil.de19a23,
            de24a29: perfil.de24a29,
            de30a35: perfil.de30a35,
            fundamental: escolaridade.fundamental,
            medio: escolaridade.medio,
            tecnico: escolaridade.tecnico,
            superior: escolaridade.superior,
            pos: escolaridade.pos,
            trilha_cnm: discipulado.trilha_cnm,
            discipulando_cnm: discipulado.discipulando_cnm,
            discipulando_outro: discipulado.discipulando_outro,
            sendo_discipulados: discipulado.sendo_discipulados,
            social: programacoes.social,
            evangelistico: programacoes.evangelistico ?? programacoes.evangelistica,
            espiritual: programacoes.espiritual,
            recreativo: programacoes.recreativo,
            oracao: programacoes.oracao
        };
    };

    window.feMontarGraficosResumo = function(resumo, options) {
        options = options || {};
        var prefix = options.prefix || '';
        var storeName = options.store || 'feResumoCharts';
        resumo = resumo || {};

        feDestroyChartStore(storeName);

        var id = function(name) {
            return feChartId(prefix, name);
        };

        feMontarGrafico(id('genero'), {
            type: 'doughnut',
            data: {
                labels: ['Homens', 'Mulheres'],
                datasets: [{
                    data: [feInt(resumo.homens), feInt(resumo.mulheres)],
                    backgroundColor: ['#2E43A0', '#DCE0F5'],
                    borderWidth: 0
                }]
            },
            options: Object.assign({ cutout: '68%', maintainAspectRatio: false }, feChartDefaults())
        }, storeName);

        feMontarGrafico(id('tipo'), {
            type: 'doughnut',
            data: {
                labels: ['Ativos', 'Cooperadores'],
                datasets: [{
                    data: [feInt(resumo.ativos), feInt(resumo.cooperadores)],
                    backgroundColor: ['#3F8358', '#DCE0F5'],
                    borderWidth: 0
                }]
            },
            options: Object.assign({ cutout: '68%', maintainAspectRatio: false }, feChartDefaults())
        }, storeName);

        feMontarGrafico(id('escolaridade'), {
            type: 'bar',
            data: {
                labels: ['Fund.', 'Médio', 'Técnico', 'Superior', 'Pós'],
                datasets: [{
                    data: [feInt(resumo.fundamental), feInt(resumo.medio), feInt(resumo.tecnico), feInt(resumo.superior), feInt(resumo.pos)],
                    backgroundColor: '#B9862F',
                    borderRadius: 5,
                    maxBarThickness: 30
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: '#767B94' }, grid: { color: '#E7E7EE' } },
                    x: { ticks: { color: '#767B94' }, grid: { display: false } }
                }
            }
        }, storeName);

        feMontarGrafico(id('faixa'), {
            type: 'bar',
            data: {
                labels: ['< 19', '19–23', '24–29', '30–35'],
                datasets: [{
                    data: [feInt(resumo.menor19), feInt(resumo.de19a23), feInt(resumo.de24a29), feInt(resumo.de30a35)],
                    backgroundColor: '#3D57C4',
                    borderRadius: 5,
                    maxBarThickness: 30
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: '#767B94' }, grid: { color: '#E7E7EE' } },
                    x: { ticks: { color: '#767B94' }, grid: { display: false } }
                }
            }
        }, storeName);

        feMontarGrafico(id('programacoes'), {
            type: 'bar',
            data: {
                labels: ['Social', 'Evang.', 'Espiritual', 'Recreativo', 'Oração'],
                datasets: [{
                    data: [feInt(resumo.social), feInt(resumo.evangelistico), feInt(resumo.espiritual), feInt(resumo.recreativo), feInt(resumo.oracao)],
                    backgroundColor: ['#2E43A0', '#3D57C4', '#3F8358', '#B9862F', '#767B94'],
                    borderRadius: 5,
                    maxBarThickness: 28
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: '#767B94' }, grid: { color: '#E7E7EE' } },
                    x: { ticks: { color: '#767B94' }, grid: { display: false } }
                }
            }
        }, storeName);

        feMontarGrafico(id('discipulado'), {
            type: 'bar',
            data: {
                labels: ['Trilha CNM', 'Método CNM', 'Outro', 'Discipulados'],
                datasets: [{
                    data: [
                        feInt(resumo.trilha_cnm),
                        feInt(resumo.discipulando_cnm),
                        feInt(resumo.discipulando_outro),
                        feInt(resumo.sendo_discipulados)
                    ],
                    backgroundColor: '#3D57C4',
                    borderRadius: 5,
                    maxBarThickness: 28
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: '#767B94' }, grid: { color: '#E7E7EE' } },
                    x: { ticks: { color: '#767B94' }, grid: { display: false } }
                }
            }
        }, storeName);
    };

    window.feRenderTotalizadorGraficos = function(data) {
        var payload = window.feResumoFromTotalizador(data);
        requestAnimationFrame(function() {
            window.feMontarGraficosResumo(payload, {
                prefix: 'vigente',
                store: 'feVigenteCharts'
            });
        });
    };

    window.feRenderResumo = function(resumo) {
        resumo = resumo || {};
        $('#formulario_ump').hide();

        feSetText('ano_referencia', resumo.ano_referencia || '—');
        feSetText('resumo-total-socios', resumo.total_socios ?? (feInt(resumo.ativos) + feInt(resumo.cooperadores)));
        feSetText('ativos', resumo.ativos ?? 0);
        feSetText('cooperadores', resumo.cooperadores ?? 0);
        feSetText('aci', feMoney(resumo.aci));
        feSetText('resumo-total-programacoes', resumo.total_programacoes ?? 0);

        var fezRepasse = String(resumo.aci_repasse || 'N').toUpperCase() === 'S';
        $('#aci-repasse-nota').text(fezRepasse ? 'Repasse confirmado no ano.' : 'Nenhum repasse registrado no ano.');

        if (resumo.tem_estrutura) {
            $('#hero-estrutura').show();
            feSetText('ump_organizada', resumo.ump_organizada ?? 0);
            feSetText('ump_nao_organizada', resumo.ump_nao_organizada ?? 0);
            if (feInt(resumo.federacao_organizada) > 0) {
                $('#hero-federacao-organizada').removeClass('fe-hidden');
                feSetText('federacao_organizada', resumo.federacao_organizada);
            } else {
                $('#hero-federacao-organizada').addClass('fe-hidden');
            }
            if (feInt(resumo.federacao_nao_organizada) > 0) {
                $('#hero-federacao-nao').removeClass('fe-hidden');
                feSetText('federacao_nao_organizada', resumo.federacao_nao_organizada);
            } else {
                $('#hero-federacao-nao').addClass('fe-hidden');
            }
        } else {
            $('#hero-estrutura').hide();
        }

        [
            'solteiros', 'casados', 'divorciados', 'viuvos', 'filhos',
            'surdos', 'auditiva', 'cegos', 'baixa_visao', 'fisica_inferior', 'fisica_superior', 'neurologico', 'intelectual',
            'trilha_cnm', 'discipulando_cnm', 'discipulando_outro', 'sendo_discipulados',
            'treinamentos_promovidos', 'treinamentos_participados_federacao', 'treinamentos_participados_sinodal', 'treinamentos_participados_cnm'
        ].forEach(function(id) {
            feSetText(id, resumo[id] ?? 0);
        });

        window.feMontarGraficosResumo(resumo, { store: 'feResumoCharts' });

        $('#resumo-card').show();
    };
})(jQuery);
</script>
