@php $totalizador = $totalizador ?? DashboardHelper::getTotalizadores(); @endphp
<div class="exec-section-title">
    <h3>Estrutura da denominação</h3>
    <span class="info">Dados de referência, atualizados automaticamente</span>
</div>
<div class="exec-panel struct-strip">
    <div class="row">
        <div class="col-6 col-md-4 col-xl text-center mb-3 mb-xl-0">
            <a class="struct-item" href="{{ route('dashboard.sinodais.index') }}">
                <div class="n">{{ $totalizador['total_sinodos'] }}</div>
                <div class="l">Sínodos</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl text-center mb-3 mb-xl-0">
            <a class="struct-item" href="{{ route('dashboard.detalhamento.index', 'presbiterio') }}">
                <div class="n">{{ $totalizador['total_presbiterios'] }}</div>
                <div class="l">Presbitérios</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl text-center mb-3 mb-xl-0">
            <a class="struct-item" href="{{ route('dashboard.detalhamento.index', 'igrejas') }}">
                <div class="n">{{ $totalizador['total_igrejas'] }}</div>
                <div class="l">Igrejas</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl text-center mb-3 mb-xl-0">
            <a class="struct-item" href="{{ route('dashboard.sinodais.index') }}?organizadas=1">
                <div class="n">{{ $totalizador['total_sinodais'] }}</div>
                <div class="l">Sinodais organizadas</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl text-center mb-3 mb-xl-0">
            <a class="struct-item" href="{{ route('dashboard.detalhamento.index', 'presbiterio') }}?organizadas=1">
                <div class="n">{{ $totalizador['total_federacoes'] }}</div>
                <div class="l">Federações organizadas</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl text-center mb-3 mb-xl-0">
            <a class="struct-item" href="{{ route('dashboard.detalhamento.index', 'igrejas') }}?organizadas=1">
                <div class="n">{!! $totalizador['total_umps'] !!}</div>
                <div class="l">UMPs organizadas</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl text-center mb-3 mb-xl-0">
            <a class="struct-item warn" href="{{ route('dashboard.detalhamento.index', 'sem_sociedades') }}">
                <div class="n">{{ $totalizador['total_n_sociedades_internas'] }}</div>
                <div class="l">
                    Igrejas sem sociedade
                    <span class="info-icon">i<span class="tt">Igrejas que não utilizam o modelo de sociedades internas da UMP.</span></span>
                </div>
            </a>
        </div>
    </div>
</div>
