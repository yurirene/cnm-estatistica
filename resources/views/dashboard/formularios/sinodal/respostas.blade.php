
<div class="row mt-3" id="resumo-card" style="display: none;">
    <div class="col-xl-12 mb-5 mb-xl-0">
        <div class="card shadow p-3">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Relatório Estatístico</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @include('dashboard.formularios.local.respostas.resumo')
                <div class="row mt-3">
                    <div class="col-md-6 mt-3">
                        @include('dashboard.formularios.graficos.grafico-perfil')
                    </div>
                    <div class="col-md-6 mt-3">
                        @include('dashboard.formularios.graficos.grafico-escolaridade')
                    </div>
                    <div class="col-md-6 mt-3">
                        @include('dashboard.formularios.graficos.grafico-programacao')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>