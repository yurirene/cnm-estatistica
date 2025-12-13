@extends('layouts.app')

@section('content')


@include('dashboard.partes.head', [
    'titulo' => 'Congresso Nacional'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-pills" id="cn-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ session()->get('aba') == 1 || !session()->has('aba') ? 'active' : '' }}"
                                id="cn-delegados-tab"
                                data-toggle="pill"
                                href="#cn-delegados"
                                role="tab"
                                aria-controls="cn-delegados"
                                aria-selected="false">
                                Delegados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ session()->get('aba') == 1 ? 'active' : '' }}"
                                id="cn-documentos-tab"
                                data-toggle="pill"
                                href="#cn-documentos"
                                role="tab"
                                aria-controls="cn-documentos"
                                aria-selected="true">
                                Documentos
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="cn-tabContent">
                        <div
                            class="tab-pane fade {{ session()->get('aba') == 0 || !session()->has('aba') ? 'active show' : ''}}"
                            id="cn-delegados"
                            role="tabpanel"
                            aria-labelledby="cn-delegados-tab"
                        >
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    @if(isset($limiteAtingido) && $limiteAtingido)
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-ban"></i> Limite de 1 delegado atingido
                                        </button>
                                        <small class="text-muted d-block mt-2">
                                            Você já possui {{ $totalDelegados ?? 0 }} delegado(s) cadastrado(s). O limite máximo é de 1 delegado por sinodal.
                                        </small>
                                    @else
                                        <a href="{{ route('dashboard.cn.sinodal.delegado.create') }}" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Cadastrar Delegado
                                        </a>
                                        @if(isset($totalDelegados))
                                            <small class="text-muted d-block mt-2">
                                                Você possui {{ $totalDelegados }} de 1 delegado(s) cadastrado(s).
                                            </small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @include('dashboard.congresso-nacional.sinodal.delegados')
                        </div>
                        <div
                            class="tab-pane fade {{ session()->get('aba') == 1 ? 'active show' : ''}}"
                            id="cn-documentos"
                            role="tabpanel"
                            aria-labelledby="cn-documentos-tab"
                        >
                            @include('dashboard.congresso-nacional.sinodal.documentos')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')


@endpush
