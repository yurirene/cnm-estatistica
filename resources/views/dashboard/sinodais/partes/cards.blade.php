
<div class="card card-stats mb-4 mb-xl-0 shadow h-100">
    <div class="card-header h-100">
        <div class="row">
                <div class="col text-center">
                    <h4 class="card-title text-uppercase text-muted mb-0">{{$sigla}} -
                        <small>
                            <span class="badge badge-pill badge-{{ $status ? 'success' : 'danger' }}">
                            {{ $status ? 'Ativo' : 'Inativo' }}
                            </span>
                        </small></h4>
                    <span class="h5 font-weight-bold mb-0">{{$nome}}</span>
                </div>
            </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <ul class="list-unstyled ">
                    <li class="py-1">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="badge badge-circle badge-info mr-3">
                                    <i class="fas fa-church"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">
                                    Nº UMPs: {{$numero_umps}}
                                    <sup>
                                        <em
                                            class="fas fa-1x fa-info-circle"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="{{
                                                $origemRelatorio
                                                    ? 'Informação retirada do último relatório estatístico preenchido'
                                                    : 'Informação retirada dos dados cadastrados'
                                            }}"
                                        ></em>
                                    </sup>
                                </h6>
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
                                <h6 class="mb-1">Nº Sócios: {{$numero_socios}} </h6>
                            </div>
                        </div>
                    </li>
                    <li class="py-1">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="badge badge-circle badge-primary mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">
                                    Att Diretoria: {{ $attDiretoria }}
                                    <sup>
                                        <em
                                            class="fas fa-1x fa-info-circle"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Última atualização da diretoria"
                                        ></em>
                                    </sup>
                                </h6>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-footer p-2">
        <div class="row">
            <div class="col text-center">
                <button type="button" class="btn btn-info btn-sm"
                    data-toggle="modal"
                    data-target="#modal_informacoes_federacoes"
                    data-nome="{{$nome}}"
                    data-id="{{ $id }}"
                    data-usuario="{{ $usuario }}"
                    data-usuarioid="{{ $usuarioId }}"
                >
                    <i class="fas fa-plus"></i> Info
                  </button>
            </div>
        </div>
    </div>
</div>
