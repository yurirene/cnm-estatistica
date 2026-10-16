<div class="row">
    <div class="col-xl-4 mt-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="painel-mini-label">Taxa de repasse — UMPs</div>
                    <div class="h2 mb-0" id="fin_umps_taxa">—</div>
                    <div class="painel-note mb-0" id="fin_umps_detalhe">—</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mt-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="painel-mini-label">Taxa de repasse — Federações</div>
                    <div class="h2 mb-0" id="fin_federacoes_taxa">—</div>
                    <div class="painel-note mb-0" id="fin_federacoes_detalhe">—</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mt-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="painel-mini-label">Taxa de repasse — Sinodais</div>
                    <div class="h2 mb-0" id="fin_sinodais_taxa">—</div>
                    <div class="painel-note mb-0" id="fin_sinodais_detalhe">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mt-3">
        <div class="card shadow">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Repasse de ACI por nível</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart painel-chart-tall"><canvas id="repasse_aci"></canvas></div>
                <p class="painel-note mb-0">Quantidade de instâncias que informaram ter ou não ter feito o repasse. Valores em reais não entram neste recorte nacional.</p>
            </div>
        </div>
    </div>
</div>
