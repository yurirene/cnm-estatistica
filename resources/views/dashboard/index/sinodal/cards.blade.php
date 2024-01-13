<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card card-stats mb-4 mb-xl-0 h-100">
                        <div class="card-header h-100">
                            <div class="row  d-flex align-items-center">
                                <div class="col-8">
                                    <h5 class="card-title text-uppercase text-muted mb-0">
                                        Total de Presbitérios
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <span class="h2 font-weight-bold mb-0">
                                        {{ $totalizador['total_presbiterios'] }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <a href="{{route('dashboard.detalhamento.index', 'presbiterio')}}"
                                        class="btn btn-sm btn-link detalhe" data-tipo="total_sinodos"
                                    >
                                        <i class="fas fa-plus"></i> Ver Mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card card-stats mb-4 mb-xl-0 h-100">
                        <div class="card-header h-100">
                            <div class="row  d-flex align-items-center">
                                <div class="col-8">
                                    <h5 class="card-title text-uppercase text-muted mb-0">
                                        Total de Igrejas
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                        <i class="fas fa-church"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <span class="h2 font-weight-bold mb-0">
                                        {{ $totalizador['total_igrejas'] }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <a href="{{route('dashboard.detalhamento.index', 'igrejas')}}"
                                        class="btn btn-sm btn-link detalhe" data-tipo="total_sinodos"
                                    >
                                        <i class="fas fa-plus"></i> Ver Mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card card-stats mb-4 mb-xl-0 h-100">
                        <div class="card-header h-100">
                            <div class="row  d-flex align-items-center">
                                <div class="col-8">
                                    <h5 class="card-title text-uppercase text-muted mb-0">
                                        Não Utilizam o Modelo de Sociedades Internas
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <span class="h2 font-weight-bold mb-0">
                                        {{ $totalizador['total_n_sociedades_internas'] }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('dashboard.detalhamento.index', 'sem_sociedades') }}"
                                        class="btn btn-sm btn-link detalhe" data-tipo="total_sinodos"
                                    >
                                        <i class="fas fa-plus"></i> Ver Mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card card-stats mb-4 mb-xl-0 h-100">
                        <div class="card-header h-100">
                            <div class="row  d-flex align-items-center">
                                <div class="col-8">
                                    <h5 class="card-title text-uppercase text-muted mb-0">
                                        Total de Sócios
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <span class="h2 font-weight-bold mb-0">
                                        {!! $totalizador['total_socios'] !!}
                                    </span>
                                </div>
                                {{-- <div class="col-6">
                                    <a href="{{ route('dashboard.detalhamento.index', 'sem_sociedades') }}"
                                        class="btn btn-sm btn-link detalhe" data-tipo="total_sinodos"
                                    >
                                        <i class="fas fa-plus"></i> Ver Mais
                                    </a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card card-stats mb-4 mb-xl-0 h-100">
                        <div class="card-header h-100">
                            <div class="row  d-flex align-items-center">
                                <div class="col-8">
                                    <h5 class="card-title text-uppercase text-muted mb-0">
                                        Total de Federações Organizadas
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <span class="h2 font-weight-bold mb-0">
                                        {!! $totalizador['total_federacoes'] !!}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('dashboard.detalhamento.index', 'presbiterio') }}?organizadas=1"
                                        class="btn btn-sm btn-link detalhe" data-tipo="total_sinodos"
                                    >
                                        <i class="fas fa-plus"></i> Ver Mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card card-stats mb-4 mb-xl-0 h-100">
                        <div class="card-header h-100">
                            <div class="row  d-flex align-items-center">
                                <div class="col-8">
                                    <h5 class="card-title text-uppercase text-muted mb-0">
                                        Total de UMPs Locais Organizadas
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <span class="h2 font-weight-bold mb-0">
                                        {!! $totalizador['total_umps'] !!}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('dashboard.detalhamento.index', 'igrejas') }}?organizadas=1"
                                        class="btn btn-sm btn-link detalhe" data-tipo="total_sinodos"
                                    >
                                        <i class="fas fa-plus"></i> Ver Mais
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
