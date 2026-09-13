<div class="row">
    <div class="col-xl-12 mt-3">
        <div class="card shadow">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Sócios com deficiência, por categoria</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart painel-chart-tall"><canvas id="deficiencias"></canvas></div>
                <p class="painel-note mb-0">Agrupado por categoria-mãe (Visual, Auditiva, Física, Neuro/Intelectual).</p>
            </div>
        </div>
    </div>
</div>

<div class="row" id="inclusao-kpis">
    <div class="col-xl-3 col-md-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="painel-mini-label">Visual</div>
                    <div class="h2 mb-0" id="incl_visual_total">—</div>
                </div>
                <div class="painel-pct-chip" id="incl_visual_pct">—</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="painel-mini-label">Auditiva</div>
                    <div class="h2 mb-0" id="incl_auditiva_total">—</div>
                </div>
                <div class="painel-pct-chip" id="incl_auditiva_pct">—</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="painel-mini-label">Física</div>
                    <div class="h2 mb-0" id="incl_fisica_total">—</div>
                </div>
                <div class="painel-pct-chip" id="incl_fisica_pct">—</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="painel-mini-label">Neuro/Intelectual</div>
                    <div class="h2 mb-0" id="incl_neuro_total">—</div>
                </div>
                <div class="painel-pct-chip" id="incl_neuro_pct">—</div>
            </div>
        </div>
    </div>
</div>
