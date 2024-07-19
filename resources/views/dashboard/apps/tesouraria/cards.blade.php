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
                                    <h5
                                        class="card-title text-uppercase text-muted mb-0"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Refente ao mês {{ $mesPassado }}"
                                    >
                                        Saldo Inicial
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <span class="h2 font-weight-bold mb-0">
                                        R${{ $totalizadores['saldoInicial'] ?? 0  }}
                                    </span>
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
                                    <h5
                                        class="card-title text-uppercase text-muted mb-0"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Refente ao mês {{ date('m/Y') }}"
                                    >
                                        Entradas
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <span class="h2 font-weight-bold mb-0">
                                        R${{ $totalizadores['entradas'] ?? 0  }}
                                    </span>
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
                                    <h5
                                        class="card-title text-uppercase text-muted mb-0"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Refente ao mês {{ date('m/Y') }}"
                                    >
                                        Saídas
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <span class="h2 font-weight-bold mb-0">
                                        R${{ $totalizadores['saidas'] ?? 0  }}
                                    </span>
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
                                    <h5
                                        class="card-title text-uppercase text-muted mb-0"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Refente ao mês {{ date('m/Y') }}"
                                    >
                                        Saldo Final
                                    </h5>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <span class="h2 font-weight-bold mb-0">
                                        R${{ $totalizadores['saldoFinal'] ?? 0  }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
