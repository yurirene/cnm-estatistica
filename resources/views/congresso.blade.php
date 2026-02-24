
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de Quórum - Delegados e Comissões</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #FFFFFF;
            color: #1F2937;
            line-height: 1.5;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-weight: 600;
        }

        h1 {
            font-size: 1.875rem;
            margin-bottom: 0.5rem;
        }

        h2 {
            font-size: 1.5rem;
        }

        h3 {
            font-size: 1.25rem;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Header */
        .header {
            margin-bottom: 2rem;
        }

        .header h1 {
            color: #1F2937;
            font-weight: 700;
        }

        .header p {
            color: #6B7280;
            font-size: 0.95rem;
        }

        /* Card */
        .card {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 0.65rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Quórum Dashboard */
        .quorum-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .quorum-card {
            background: linear-gradient(135deg, #F0FDFA 0%, #FFFFFF 100%);
            border: 2px solid #0F766E;
            border-radius: 0.65rem;
            padding: 1.5rem;
            text-align: center;
        }

        .quorum-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .quorum-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0F766E;
            margin-bottom: 0.5rem;
        }

        .quorum-requirement {
            font-size: 0.875rem;
            color: #6B7280;
        }

        /* Progress Bar */
        .progress-container {
            margin-top: 1rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #E5E7EB;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #0F766E;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-fill.warning {
            background-color: #F59E0B;
        }

        .progress-fill.success {
            background-color: #10B981;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        .status-badge.success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-badge.warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-badge.danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        /* Regiões */
        .regioes-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .regiao-section {
            border-top: 3px solid #0F766E;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }

        .regiao-section:first-of-type {
            border-top: none;
            margin-top: 0;
            padding-top: 0;
        }

        .regiao-header {
            margin-bottom: 1.5rem;
        }

        .regiao-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 0.5rem;
        }

        .regiao-stats {
            display: flex;
            gap: 2rem;
            font-size: 0.875rem;
            color: #6B7280;
        }

        .regiao-stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .regiao-stat-value {
            font-weight: 600;
            color: #1F2937;
        }

        /* Sinodal Cards Grid */
        .sinodais-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .sinodal-card {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 0.65rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .sinodal-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-color: #0F766E;
        }

        .sinodal-header {
            background: linear-gradient(135deg, #0F766E 0%, #0D5E57 100%);
            color: #FFFFFF;
            padding: 1rem;
            border-bottom: 1px solid #0D5E57;
        }

        .sinodal-name {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .sinodal-representante {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .sinodal-content {
            padding: 1rem;
        }

        .federacoes-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .federacao-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .federacao-name {
            font-weight: 500;
            color: #1F2937;
        }

        .federacao-count {
            background-color: #0F766E;
            color: #FFFFFF;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .empty-federacoes {
            text-align: center;
            padding: 1rem;
            color: #9CA3AF;
            font-size: 0.875rem;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-sinodal {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .badge-federacao {
            background-color: #DCFCE7;
            color: #166534;
        }

        /* Responsive */
        @media (max-width: 1023px) {
            .container {
                padding: 1rem;
            }

            .quorum-dashboard {
                grid-template-columns: 1fr;
            }

            .sinodais-grid {
                grid-template-columns: 1fr;
            }

            .regiao-stats {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        /* Link de navegação */
        .nav-link {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.625rem 1rem;
            background-color: #F3F4F6;
            color: #0F766E;
            text-decoration: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: #0F766E;
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Monitoramento de Quórum</h1>
            <p>Acompanhe a distribuição de delegados por região, sinodal e federação</p>
        </div>

        <!-- Quórum Dashboard -->
        <div class="quorum-dashboard" id="quorumDashboard"></div>

        <!-- Regiões -->
        <div class="regioes-container" id="regioesContainer"></div>
    </div>

    <script>
        // Dados do PHP
        const totalizador = @json($totalizador);
        const sinodaisComFederacoes = @json($listaSinodaisComFederacoes);

        // Agrupar sinodais por região
        const sinodaisPorRegiao = {};
        sinodaisComFederacoes.forEach(sinodal => {
            const regiaoNome = sinodal.regiao?.nome || 'Sem Região';
            if (!sinodaisPorRegiao[regiaoNome]) {
                sinodaisPorRegiao[regiaoNome] = [];
            }
            sinodaisPorRegiao[regiaoNome].push(sinodal);
        });

        // Calcular totais por região
        function calcularTotaisPorRegiao(regiaoNome) {
            const sinodaisRegiao = sinodaisPorRegiao[regiaoNome] || [];
            let totalDelegados = 0;
            let totalFederacoes = 0;

            sinodaisRegiao.forEach(sinodal => {
                totalDelegados += parseInt(sinodal.total_delegados_sinodal || 0);
                totalFederacoes += sinodal.federacoes?.length || 0;
                sinodal.federacoes?.forEach(fed => {
                    totalDelegados += parseInt(fed.total_delegados || 0);
                });
            });

            return {
                totalDelegados,
                totalSinodais: sinodaisRegiao.length,
                totalFederacoes
            };
        }

        // Render: Quórum Dashboard
        function renderQuorumDashboard() {
            const container = document.getElementById('quorumDashboard');
            const quorum = totalizador;

            const getStatusClass = (atingido) => atingido ? 'success' : 'warning';
            const getStatusText = (atingido) => atingido ? 'Atingido' : 'Pendente';

            const totalDelegados = quorum.sinodais_com_delegado + quorum.federacoes_com_delegado;
            const percentualSinodais = quorum.total_sinodais > 0 ? Math.round((quorum.sinodais_com_delegado / quorum.quorum_sinodais) * 100) : 0;
            const percentualFederacoes = quorum.total_federacoes > 0 ? Math.round((quorum.federacoes_com_delegado / quorum.quorum_federacoes) * 100) : 0;
            const quorumAtingido = quorum.atingiu_quorum_sinodais && quorum.atingiu_quorum_federacoes;

            container.innerHTML = `
                <div class="quorum-card">
                    <div class="quorum-label">Sinodais</div>
                    <div class="quorum-value">${quorum.sinodais_com_delegado}/${quorum.quorum_sinodais}</div>
                    <div class="quorum-requirement">Total: ${quorum.total_sinodais} sinodais</div>
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Progresso</span>
                            <span>${percentualSinodais}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill ${getStatusClass(quorum.atingiu_quorum_sinodais)}"
                                 style="width: ${Math.min(percentualSinodais, 100)}%"></div>
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <span class="status-badge ${getStatusClass(quorum.atingiu_quorum_sinodais)}">${getStatusText(quorum.atingiu_quorum_sinodais)}</span>
                        </div>
                    </div>
                </div>

                <div class="quorum-card">
                    <div class="quorum-label">Federações</div>
                    <div class="quorum-value">${quorum.federacoes_com_delegado}/${quorum.quorum_federacoes}</div>
                    <div class="quorum-requirement">Mínimo 1/3 das federações (Total: ${quorum.total_federacoes})</div>
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Progresso</span>
                            <span>${percentualFederacoes}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill ${getStatusClass(quorum.atingiu_quorum_federacoes)}"
                                 style="width: ${Math.min(percentualFederacoes, 100)}%"></div>
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <span class="status-badge ${getStatusClass(quorum.atingiu_quorum_federacoes)}">${getStatusText(quorum.atingiu_quorum_federacoes)}</span>
                        </div>
                    </div>
                </div>

                <div class="quorum-card" style="background: linear-gradient(135deg, ${quorumAtingido ? '#D1FAE5' : '#FEE2E2'} 0%, #FFFFFF 100%); border-color: ${quorumAtingido ? '#10B981' : '#DC2626'};">
                    <div class="quorum-label">Status Geral</div>
                    <div class="quorum-value" style="color: ${quorumAtingido ? '#10B981' : '#DC2626'};">
                        ${quorumAtingido ? '✓ Quórum Atingido' : '✗ Quórum Pendente'}
                    </div>
                    <div class="quorum-requirement">
                        ${quorumAtingido ? 'Todos os requisitos foram atendidos' : 'Faltam delegados para atingir o quórum'}
                    </div>
                </div>
            `;
        }

        // Render: Regiões
        function renderRegioes() {
            const container = document.getElementById('regioesContainer');
            const regioes = Object.keys(sinodaisPorRegiao).sort();

            container.innerHTML = regioes.map(regiaoNome => {
                const sinodaisRegiao = sinodaisPorRegiao[regiaoNome] || [];
                const totais = calcularTotaisPorRegiao(regiaoNome);

                return `
                    <div class="regiao-section">
                        <div class="regiao-header">
                            <h2 class="regiao-title">Região ${regiaoNome}</h2>
                            <div class="regiao-stats">
                                <div class="regiao-stat">
                                    <span>Delegados:</span>
                                    <span class="regiao-stat-value">${totais.totalDelegados}</span>
                                </div>
                                <div class="regiao-stat">
                                    <span>Sinodais:</span>
                                    <span class="regiao-stat-value">${totais.totalSinodais}</span>
                                </div>
                                <div class="regiao-stat">
                                    <span>Federações:</span>
                                    <span class="regiao-stat-value">${totais.totalFederacoes}</span>
                                </div>
                            </div>
                        </div>

                        <div class="sinodais-grid">
                            ${sinodaisRegiao.map(sinodal => {
                                const delegadosSinodal = parseInt(sinodal.total_delegados_sinodal || 0);
                                const federacoes = sinodal.federacoes || [];

                                return `
                                    <div class="sinodal-card">
                                        <div class="sinodal-header">
                                            <div class="sinodal-name">${sinodal.nome}</div>
                                            <div class="sinodal-representante">${sinodal.sigla || ''}</div>
                                        </div>
                                        <div class="sinodal-content">
                                            <div style="margin-bottom: 1rem;">
                                                <span class="badge badge-sinodal">Sinodal</span>
                                                <span style="margin-left: 0.5rem; font-size: 0.875rem; color: #6B7280;">
                                                    ${delegadosSinodal} delegado${delegadosSinodal !== 1 ? 's' : ''}
                                                </span>
                                            </div>

                                            ${federacoes.length > 0 ? `
                                                <div>
                                                    <div style="font-size: 0.75rem; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                                                        Federações
                                                    </div>
                                                    <div class="federacoes-list">
                                                        ${federacoes.map(federacao => {
                                                            const countFed = parseInt(federacao.total_delegados || 0);
                                                            return `
                                                                <div class="federacao-item">
                                                                    <span class="federacao-name">${federacao.nome}</span>
                                                                    <span class="federacao-count">${countFed}</span>
                                                                </div>
                                                            `;
                                                        }).join('')}
                                                    </div>
                                                </div>
                                            ` : `
                                                <div class="empty-federacoes">Nenhuma federação cadastrada</div>
                                            `}
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Render all
        function render() {
            renderQuorumDashboard();
            renderRegioes();
        }

        // Initial render
        render();
    </script>
</body>
</html>
