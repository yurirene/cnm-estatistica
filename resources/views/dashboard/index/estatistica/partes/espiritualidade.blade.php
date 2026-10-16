<div class="row">
    <div class="col-xl-12 mt-3">
        <div class="card shadow">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Programações por nível — distribuição percentual</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart painel-chart-tall"><canvas id="programacoes"></canvas></div>
                <p class="painel-note mb-0">Comparação normalizada (100%) entre os três níveis — os volumes absolutos têm escalas muito diferentes.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mt-3">
        <div class="card shadow">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Discipulado em relação ao total de sócios</h6>
            </div>
            <div class="card-body">
                <div class="painel-funnel" id="funil-discipulado"></div>
                <p class="painel-note mb-0">Cada faixa é o percentual sobre o total de sócios. Os campos não são etapas aninhadas — a mesma pessoa pode aparecer em mais de um.</p>
            </div>
        </div>
    </div>
</div>
