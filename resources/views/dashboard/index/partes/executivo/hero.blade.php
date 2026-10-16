@php
    $hero = DashboardHelper::getDashboardHero();
    $saude = $hero['saude'];
    $socios = $hero['socios'];
    $aci = $hero['aci'];
    $alertas = $hero['alertas'];
@endphp
<div class="row">
    <div class="col-xl-3 col-lg-6 mt-3">
        <div class="exec-hero-card h-100">
            <div class="l">
                Saúde geral da rede
                <button type="button" class="exec-hint" aria-label="Como é calculada a saúde geral da rede">
                    i
                    <span class="exec-hint-tooltip" role="tooltip">
                        Índice de 0 a 100 que resume a situação da rede no ano:
                        <strong>40%</strong> taxa de resposta dos formulários das UMPs,
                        <strong>40%</strong> ACI arrecadada em relação à meta e
                        <strong>20%</strong> crescimento de sócios frente ao ano anterior.
                        <span class="exec-hint-ok">Estado aceitável: 80 ou mais (Saudável).</span>
                        De 50 a 79 exige atenção; abaixo de 50 é crítico.
                    </span>
                </button>
            </div>
            <div class="exec-health">
                <div class="exec-health-ring">
                    <svg width="76" height="76">
                        <circle cx="38" cy="38" r="32" fill="none" stroke="var(--color-pale)" stroke-width="8"/>
                        <circle cx="38" cy="38" r="32" fill="none" stroke="{{ $saude['cor'] }}" stroke-width="8"
                            stroke-dasharray="201" stroke-dashoffset="{{ $saude['dashoffset'] }}" stroke-linecap="round"/>
                    </svg>
                    <div class="val" style="color: {{ $saude['cor'] }}">{{ $saude['score'] }}</div>
                </div>
                <div class="exec-health-text">
                    <div class="t">{{ $saude['rotulo'] }}</div>
                    <div class="d">Combina taxa de resposta, repasse de ACI e crescimento de sócios.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 mt-3">
        <div class="exec-hero-card h-100">
            <div class="l">Sócios reportados ({{ $hero['ano'] }})</div>
            <div class="exec-trend-num">
                <span class="n">{{ $socios['total_formatado'] }}</span>
                <span class="exec-trend-badge {{ $socios['positiva'] ? 'good' : 'bad' }}">{{ $socios['variacao_formatada'] }}</span>
            </div>
            <div class="exec-mini-chart"><canvas id="chartMiniTrend"></canvas></div>
            <div class="exec-hero-note">vs. mesmo período de {{ $hero['ano_anterior'] }}</div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 mt-3">
        <div class="exec-hero-card h-100">
            <div class="l">ACI arrecadada</div>
            <div class="exec-trend-num"><span class="n">{{ $aci['arrecadada_formatada'] }}</span></div>
            <div class="exec-hero-note">{{ $aci['percentual'] }}% da meta anual ({{ $aci['meta_formatada'] }})</div>
            <div class="exec-progress"><span style="width: {{ $aci['percentual'] }}%"></span></div>
            <div class="exec-hero-note">Ano anterior: {{ $aci['anterior_formatada'] }}</div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 mt-3">
        <div class="exec-hero-card h-100">
            <div class="l">Pontos de atenção</div>
            <div class="exec-alert-count">{{ $alertas['total'] }}</div>
            <div class="exec-alert-list">
                <div class="a">Sinodais com 0% de resposta <b>{{ $alertas['zero_resposta'] }}</b></div>
                <div class="a">Sem repasse de ACI <b>{{ $alertas['sem_repasse'] }}</b></div>
            </div>
            <div class="exec-hero-note">
                Ano anterior: {{ $alertas['zero_resposta_anterior'] }} sem resposta · {{ $alertas['sem_repasse_anterior'] }} sem ACI
            </div>
        </div>
    </div>
</div>
