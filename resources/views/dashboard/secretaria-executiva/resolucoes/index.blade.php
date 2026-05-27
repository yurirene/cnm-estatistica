@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Secretaria Executiva',
    'subtitulo' => 'Resoluções e prazos',
])

<div class="container-fluid mt--7">
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted mb-0">Total</h5>
                    <span class="h2 font-weight-bold mb-0">{{ $estatisticas['total'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted mb-0">Concluídas</h5>
                    <span class="h2 font-weight-bold mb-0 text-success">{{ $estatisticas['concluidas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted mb-0">Atrasadas</h5>
                    <span class="h2 font-weight-bold mb-0 text-danger">{{ $estatisticas['atrasadas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted mb-0">Prazo em até 7 dias</h5>
                    <span class="h2 font-weight-bold mb-0 text-warning">{{ $estatisticas['proximas'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Resoluções</h3>
                        </div>
                        <div class="col-auto text-right">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modal-telegram">
                                <i class="fab fa-telegram"></i> Telegram
                            </button>
                            @if($podeGerenciar)
                            <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal-importar">
                                <i class="fas fa-file-import"></i> Importar CSV
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-resolucao" data-acao="criar">
                                <i class="fas fa-plus"></i> Nova resolução
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.secretaria-executiva.resolucoes.partials.modal-resolucao')
@include('dashboard.secretaria-executiva.resolucoes.partials.modal-importar')
@include('dashboard.secretaria-executiva.resolucoes.partials.modal-telegram')

@endsection

@push('js')
{!! $dataTable->scripts() !!}
@include('dashboard.secretaria-executiva.resolucoes.scripts')
@endpush
