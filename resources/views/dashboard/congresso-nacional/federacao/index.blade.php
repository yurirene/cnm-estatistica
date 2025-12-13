@extends('layouts.app')

@section('content')


@include('dashboard.partes.head', [
    'titulo' => 'Congresso Nacional'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-body">
                    <div class="tab-content" id="cn-tabContent">
                        <div
                            class="tab-pane fade active show"
                            id="cn-delegados"
                            role="tabpanel"
                            aria-labelledby="cn-delegados-tab"
                        >
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    @if(isset($limiteAtingido) && $limiteAtingido)
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-ban"></i> Limite de 6 delegados atingido
                                        </button>
                                        <small class="text-muted d-block mt-2">
                                            Você já possui {{ $totalDelegados ?? 0 }} delegado(s) cadastrado(s). O limite máximo é de 6 delegados por federação.
                                        </small>
                                    @else
                                        <a href="{{ route('dashboard.cn.federacao.delegado.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Cadastrar Delegado
                                        </a>
                                        @if(isset($totalDelegados))
                                            <small class="text-muted d-block mt-2">
                                                Você possui {{ $totalDelegados }} de 6 delegado(s) cadastrado(s).
                                            </small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @include('dashboard.congresso-nacional.federacao.delegados')
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
