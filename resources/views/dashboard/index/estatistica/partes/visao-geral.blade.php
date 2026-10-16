<div class="painel-insight">
    <h4>Destaques do período</h4>
    <ul id="painel-insights"></ul>
</div>

<div class="row">
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Evolução — 4 anos</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart"><canvas id="chartEvolucao"></canvas></div>
                <p class="painel-note mb-0">Série histórica da taxa de resposta das UMPs e do percentual que informou ter repassado ACI.</p>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0" id="socios-barras-titulo">Sócios por região</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-region-bars" id="socios-barras"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mt-3">
        <div class="card shadow">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Distribuição por Estado</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div id="distribuicao"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Funil de completude dos relatórios</h6>
            </div>
            <div class="card-body">
                <div class="painel-funnel" id="funil-completude"></div>
                <p class="painel-note mb-0">A queda mais acentuada costuma acontecer na base (UMPs locais).</p>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Federações — maior e menor taxa de resposta</h6>
            </div>
            <div class="card-body">
                <div class="painel-rank-list" id="ranking-federacoes"></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mt-3">
    <div class="card-header bg-transparent">
        <h6 class="text-uppercase text-muted ls-1 mb-0">Indicadores estruturais (médias)</h6>
    </div>
    <div class="card-body">
        <div class="row" id="painel-medias"></div>
    </div>
</div>
