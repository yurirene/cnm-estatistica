@php
    $qualidade = DashboardHelper::getQualidadeEntregaRelatorios();
    $variacao = $qualidade['variacao'] ?? 0;
    $variacaoTexto = ($variacao >= 0 ? '▲ ' : '▼ ') . number_format(abs($variacao), 1, ',', '.') . ' p.p.';
    $variacaoCor = $variacao >= 0 ? 'var(--color-good)' : 'var(--color-bad)';
@endphp
<div class="exec-panel h-100 d-flex flex-column">
    <h4>Qualidade da entrega — UMPs locais</h4>
    <div class="sub">
        {{ $qualidade['percentual'] ?? 0 }}% no período ·
        <span style="color: {{ $variacaoCor }}; font-weight: 600;">{{ $variacaoTexto }}</span> vs. ano anterior
    </div>
    <div class="exec-chart-wrap flex-grow-1"><canvas id="entregas"></canvas></div>
    <div class="exec-legend-row">
        <div class="exec-legend-item">
            <span class="exec-legend-dot" style="background:#2E9E8F;"></span>
            Entregue ({{ $qualidade['percentual'] ?? 0 }}%)
        </div>
        <div class="exec-legend-item">
            <span class="exec-legend-dot" style="background:#DCE0F5;"></span>
            Pendente
        </div>
    </div>
</div>
