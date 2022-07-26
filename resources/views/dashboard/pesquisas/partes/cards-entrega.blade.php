<div class="row">
    @if(isset($respostas['sinodal']))
    <div class="col-xl-4 col-lg-6 mt-3">
        <div class="card shadow card-stats mb-4 mb-xl-0 h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total de Sínodos</h5>
                        <span class="h2 font-weight-bold mb-0">
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                    <span>Respostas</span>
                                    </div>
                                    <div class="progress-percentage">
                                    <span>{{ $respostas['sinodal']['porcentagem'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $respostas['sinodal']['porcentagem'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $respostas['sinodal']['porcentagem'] }}%;"></div>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if(isset($respostas['federacao']))
    <div class="col-xl-4 col-lg-6 mt-3">
        <div class="card shadow card-stats mb-4 mb-xl-0 h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total de Federações</h5>
                        <span class="h2 font-weight-bold mb-0">
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                    <span>Respostas</span>
                                    </div>
                                    <div class="progress-percentage">
                                    <span>{{ $respostas['federacao']['porcentagem'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $respostas['federacao']['porcentagem'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $respostas['federacao']['porcentagem'] }}%;"></div>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if(isset($respostas['local']))
    <div class="col-xl-4 col-lg-6 mt-3">
        <div class="card shadow card-stats mb-4 mb-xl-0 h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total de UMPs Locais</h5>
                        <span class="h2 font-weight-bold mb-0">
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                    <span>Respostas</span>
                                    </div>
                                    <div class="progress-percentage">
                                    <span>{{ $respostas['local']['porcentagem'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $respostas['local']['porcentagem'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $respostas['local']['porcentagem'] }}%;"></div>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>