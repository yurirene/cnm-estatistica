@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Informações da ' .$federacao->sigla,
    'subtitulo' => $federacao->nome
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-5 mb-5 mb-xl-0">
            <div class="card shadow p-3 h-100">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Informações</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{route('dashboard.federacoes.index')}}"><i class="fas fa-arrow-left"></i> Voltar</a>
                    
                    <div class="row mt-3">
                        <div class="col">
                            <h3><span class="badge badge-primary">Nome:</span> {{ $federacao->nome }}</h3>
                            <h3><span class="badge badge-primary">Presbitério:</span> {{ $federacao->presbiterio }}</h3>
                            <h3><span class="badge badge-primary">Data de Organização:</span> {{ $federacao->data_organizacao_formatada }}</h3>
                            <h3><span class="badge badge-primary">Redes Sociais:</span> {{ $federacao->midias_sociais }}</h3>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <h3><span class="badge badge-primary">Último Formulário:</span> {{ $informacoes['ultimo_formulario'] }}</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>UMPs Locais Organizadas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_umps_organizada'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $informacoes['total_umps_organizada'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $informacoes['total_umps_organizada'] }}%;"></div>
                                </div>
                            </div>

                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Não adotam Sociedades Internas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_igrejas_n_sociedades'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $informacoes['total_igrejas_n_sociedades'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $informacoes['total_igrejas_n_sociedades'] }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">UMPs Cadastradas</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        @if(count($umps))
                        @foreach($umps as $ump)
                        <div class="col-lg-6 mt-3">
                            @include('dashboard.federacoes.partes.cards', $ump)
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    Morris.Donut({
        element: 'donut-example',
        data: [
            {label: "FAMP", value: 12},
            {label: "FEPAM", value: 30},
            {label: "FMS", value: 20}
        ],
        resize: true,
        formatter: function (y, data) {
            return data.value + ' Sócios'
        },
    });
    
</script>
@endpush