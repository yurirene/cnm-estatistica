
<div class="card card-stats mb-4 mb-xl-0 shadow">
    <div class="card-body">
        <div class="row">
            <div class="col text-center">
                <h4 class="card-title text-uppercase text-muted mb-0">{{$sigla}}</h4>
                <span class="h5 font-weight-bold mb-0">{{$nome}}</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <ul class="list-unstyled ">
                    <li class="py-1">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="badge badge-circle badge-info mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Nº UMPs: {{$numero_umps}}</h6>
                            </div>
                        </div>
                    </li>
                    <li class="py-1">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="badge badge-circle badge-success mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Nº Sócios: {{$numero_socios}}</h6>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <a href="#" class="btn btn-info btn-sm"><i class="fas fa-plus"></i> Info</a>
            </div>
        </div>
    </div>
</div>