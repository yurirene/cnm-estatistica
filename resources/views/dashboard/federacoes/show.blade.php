@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Informações da ' .$federacao->sigla,
    'subtitulo' => $federacao->nome,
    'botaoRetorno' => route('dashboard.federacoes.index')
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-5 mb-5 mb-xl-0">
            <div class="card shadow p-3 h-100">
                <div class="card-header border-0">
                    <div class="row d-flex justify-content-between">
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <a class="btn btn-primary btn-sm  {{
                                        is_null($navegacaoFederacoes['anterior'])
                                        ? 'disabled'
                                        : ''
                                }}"
                                href="{{route('dashboard.federacoes.show', ($navegacaoFederacoes['anterior'] ?? ''))}}"
                                >
                                    <i class="fas fa-arrow-left"></i> Anterior
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <a class="btn btn-primary btn-sm {{
                                        is_null($navegacaoFederacoes['proxima'])
                                        ? 'disabled'
                                        : ''
                                }}"
                                href="{{route('dashboard.federacoes.show', ($navegacaoFederacoes['proxima'] ?? ''))}}"
                                >
                                    <i class="fas fa-arrow-right"></i> Próxima
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body h-100">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-3 text-center">Informações</h2>
                        </div>
                    </div>
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
                <div class="card-body overflow-auto" style="max-height: 600px">
                    <div class="row mt-3">
                        @if(count($umps))
                        @foreach($umps as $ump)
                        <div class="col-md-6 col-xl-6 mt-3">
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
