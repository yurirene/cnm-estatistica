<div class="card shadow p-3 h-100">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">{{ $dados['datasets'][0]['label'] }}</h3>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <canvas id="grafico_linha_{{ str_replace(' ', '', $dados['datasets'][0]['label']) }}"></canvas>
        </div>
    </div>
</div>