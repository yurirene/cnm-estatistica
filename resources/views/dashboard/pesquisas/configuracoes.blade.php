@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Pesquisas',
    'subtitulo' => 'Configurações'
])
    
<div class="container-fluid mt--7">
   
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Configurações da Pesquisa</h3>
                        </div>
                        <div class="col">
                            <ul class="nav nav-pills justify-content-end">
                                <li class="nav-item mr-2 mr-md-0">
                                    <a href="{{ route('dashboard.pesquisas.limpar-respostas', $pesquisa->id) }}" onclick="confirmar(this)" class="btn btn-danger py-2 px-3 active">
                                        <span class="d-none d-md-block"><i class="fas fa-exclamation-triangle"></i> Limpar Respostas</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('dashboard.pesquisas.partes.painel-de-configuracao', [
                        'configuracoes' => $configuracoes,
                        'pesquisa' => $pesquisa
                    ])
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('js')

@endpush