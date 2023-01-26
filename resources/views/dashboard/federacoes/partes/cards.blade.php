
<div class="card card-stats mb-4 mb-xl-0 shadow h-100">
    <div class="card-body">
        <div class="row">
            <div class="col text-center">
                <span class="h5 font-weight-bold mb-0">{{$nome}}</span>
                <h6><small>* Informação obtida do Relatório Estatístico</small></h6>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <ul class="list-unstyled ">
                    
                    <li class="py-1">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="badge badge-circle badge-{{$status == true ?  'success' : 'danger'}} mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $status == true ? 'Nº de Sócios: ' . $numero_socios : 'Inativa'}}</h6>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <div class="badge badge-info mr-3"> {{ $ultimo_formulario }} </div>
            </div>
        </div>
    </div>
</div>