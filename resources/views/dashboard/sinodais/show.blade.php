@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Informações da ' .$sinodal->sigla,
    'subtitulo' => $sinodal->nome
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-5 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Informações</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{route('dashboard.sinodais.index')}}"><i class="fas fa-arrow-left"></i> Voltar</a>
                    
                    <div class="row mt-3">
                        <div class="col">
                            <div id="donut-example" style="max-height: 200px;">
                                
                            </div>
                            <h6 class="text-center">Representação das Federações</h6>
                        </div>
                        <div class="col">
                            <div id="donut-example" style="max-height: 200px;">
                                
                            </div>
                            <h6 class="text-center">Representação das Federações</h6>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Task completed</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>60%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                                </div>
                            </div>
                            
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Task completed</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>60%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                                </div>
                            </div>
                            
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Task completed</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>60%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
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
                            <h3 class="mb-0">Federações Jurisdicionadas</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        @if($sinodal->federacoes->count())
                        @foreach($sinodal->federacoes as $federacao)
                        <div class="col-md-4 mt-3">
                            @include('dashboard.sinodais.partes.cards', [
                            'nome' => $federacao->nome,
                            'sigla' => $federacao->sigla,
                            'numero_umps' => $federacao->locais->count(),
                            'formularios_preenchidos' => 'SEM INFO'
                            ])
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