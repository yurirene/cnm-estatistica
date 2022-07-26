@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => $pesquisa->nome
])
    
<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Relatório</h3>
                        </div>
                        <div class="col">
                            <ul class="nav nav-pills justify-content-end">
                                <li class="nav-item mr-2 mr-md-0">
                                    <a href="{{ route('dashboard.pesquisas.relatorio.excel', $pesquisa->id) }}" target="_blank" class="nav-link py-2 px-3 active">
                                        <span class="d-none d-md-block"><i class="fas fa-file-excel"></i> Exportar Excel</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('dashboard.pesquisas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                    @include('dashboard.pesquisas.partes.alcance', [
                        'alcance' => $alcance
                    ])
                    <div class="row">
                        @include('dashboard.pesquisas.partes.totalizadores', [
                            'totalizadores' => $totalizadores
                        ])
                    </div>
                    <div class="row">
                    @foreach ($graficos as $grafico)
                        <div class="col-sm-6 col-xl-{{ $grafico['tamanho'] }} mt-3">
                            {!! $grafico['grafico']['html'] !!}
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('script')
@foreach ($graficos as $grafico)
{!! $grafico['grafico']['js'] !!}

@endforeach

@endpush