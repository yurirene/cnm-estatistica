@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Tarefas',
    'subtitulo' => 'Minhas tarefas',
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
                    <h5 class="card-title text-muted mb-0">Pendentes</h5>
                    <span class="h2 font-weight-bold mb-0 text-warning">{{ $estatisticas['pendentes'] }}</span>
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
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Tarefas</h3>
                        </div>
                        <div class="col-auto text-right">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modal-telegram-tarefas">
                                <i class="fab fa-telegram"></i> Telegram
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-tarefa" data-acao="criar">
                                <i class="fas fa-plus"></i> Nova tarefa
                            </button>
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

@include('dashboard.tarefas.partials.modal-tarefa')
@include('dashboard.tarefas.partials.modal-telegram')

@endsection

@push('js')
{!! $dataTable->scripts() !!}
@include('dashboard.tarefas.scripts')
@endpush
