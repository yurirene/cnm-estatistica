
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
                                    <i class="fas fa-info"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $status == true ? 'Nº de Sócios: ' . $numeroSocios : 'Inativa'}}</h6>
                                <h6 class="mb-1">
                                    Att. Diretoria: {{ $ultimaAtualizacaoDiretoria }}
                                </h6>
                                <h6 class="mb-1">
                                    Formulário: {{ $ultimoFormulario }}
                                </h6>
                                <h6 class="mb-1">
                                    ACI: {{ $ultimoFormulario }} - {{ $ultimaACI }}
                                </h6>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="row">
            <div class="col text-center">
                <button
                    class="btn btn-sm btn-secondary mr-3"
                    data-toggle="modal"
                    data-dados="{{$diretoria}}"
                    data-target="#modal_diretoria"
                    {{$temDiretoria == false ? 'disabled' : ''}}
                >
                    <i class="fas fa-eye"></i> Diretoria
                </button>
                <a
                    href="{{ route('dashboard.formularios-local.export', $id) }}"
                    class="btn btn-sm btn-secondary mr-3 {{ $mesmoAno == false ? 'disabled' : '' }}"
                    target="_blank"
                >
                    <i class="fas fa-print"></i> Formulário
                </a>
            </div>
        </div>
    </div>
</div>