<div class="row">
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Gênero</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart"><canvas id="genero"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Tipo de sócio</h6>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="row">
                    <div class="col-6">
                        <div class="painel-percapita">
                            <div class="n" id="tipo_ativos_pct">—</div>
                            <div class="l">Ativos (<span id="tipo_ativos">—</span>)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="painel-percapita">
                            <div class="n" id="tipo_cooperadores_pct">—</div>
                            <div class="l">Cooperadores (<span id="tipo_cooperadores">—</span>)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Idade dos sócios</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart"><canvas id="idade"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mt-3">
        <div class="card shadow h-100">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Estado civil</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart"><canvas id="estado_civil"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mt-3">
        <div class="card shadow">
            <div class="card-header bg-transparent">
                <h6 class="text-uppercase text-muted ls-1 mb-0">Escolaridade</h6>
            </div>
            <div class="card-body">
                @include('dashboard.partes.skeleton')
                <div class="painel-chart painel-chart-narrow"><canvas id="escolaridade"></canvas></div>
            </div>
        </div>
    </div>
</div>
