@extends('layouts.app')

@section('content')
@include('dashboard.index.federacao.cards',[
    'totalizadores' => DashboardHelper::getTotalizadores()
])

@php $federacao = DashboardHelper::getInfo(); @endphp

<div class="container-fluid mt--7">

    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class=" mb-0">Informações</h2>
                        </div>
                        <div class="col">
                            <ul class="nav nav-pills justify-content-end">
                                <li class="nav-item mr-2 mr-md-0">
                                    <a href="#" class="nav-link py-2 px-3 active"  data-toggle="modal" data-target="#modalEditar">
                                        <span class="d-none d-md-block">Editar</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3><span class="badge badge-primary">Nome:</span> {{ $federacao->nome }}</h3>
                            <h3><span class="badge badge-primary">Presbitério:</span> {{ $federacao->presbiterio }}</h3>
                            <h3><span class="badge badge-primary">Data de Organização:</span> {{ $federacao->data_organizacao_formatada }}</h3>
                            <h3><span class="badge badge-primary">Redes Sociais:</span> {{ $federacao->midias_sociais }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-5 mb-xl-0">
            @include('dashboard.index.avisos')
        </div>

    </div>
    <div class="row">
        <div class="col-xl-12 mt-3">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class=" mb-0">Entrega de Formulários</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table id="formularios-entregues-table" class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Federação</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Informações</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::model($federacao, ['url' => route('dashboard.federacoes.update-info', $federacao->id), 'method' => 'PUT']) !!}
            <div class="modal-body">
                <div class="form-group{{ $errors->has('nome') ? ' has-error' : '' }}">
                {!! Form::label('nome', 'Nome') !!}
                {!! Form::text('nome', null, ['class' => 'form-control', 'required' => 'required']) !!}
                <small class="text-danger">{{ $errors->first('nome') }}</small>
                </div>
                <div class="form-group{{ $errors->has('presbiterio') ? ' has-error' : '' }}">
                {!! Form::label('presbiterio', 'Presbitério') !!}
                {!! Form::text('presbiterio', null, ['class' => 'form-control', 'required' => 'required']) !!}
                <small class="text-danger">{{ $errors->first('presbiterio') }}</small>
                </div>
                <div class="form-group{{ $errors->has('data_organizacao') ? ' has-error' : '' }}">
                {!! Form::label('data_organizacao', 'Data da Organização') !!}
                {!! Form::text('data_organizacao', null, ['class' => 'form-control isDate', 'required' => 'required']) !!}
                <small class="text-danger">{{ $errors->first('data_organizacao') }}</small>
                </div>
                <div class="form-group{{ $errors->has('midias_sociais') ? ' has-error' : '' }}">
                {!! Form::label('midias_sociais', 'Mídias Sociais') !!}
                {!! Form::text('midias_sociais', null, ['class' => 'form-control', 'placeholder' => '@']) !!}
                <small class="text-danger">{{ $errors->first('midias_sociais') }}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
@push('js')

@push('js')

<script>
    $(function() {
        var rotaExport = "{{ route('dashboard.formularios-local.export', ':id') }}";
        $('#formularios-entregues-table').DataTable({
            dom: 'frtip',
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.formularios-entregues", "Local") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        var imprimir = '';
                        if (result.entregue == 1) {
                            imprimir = `<a
                            href="${rotaExport.replace(':id', result.id)}"
                            class="btn btn-sm btn-primary"
                            target="_blank"
                            >
                                <i class="fas fa-print"></i>
                            </a>`;
                        }
                        return imprimir;
                    }
                },
                {data: 'nome'},
                {
                    render: function (data, type, result) {
                        return `<span class="badge bg-${result.entregue == 1 ? 'success' : 'danger'}">
                            ${result.entregue == 1 ? 'Entregue' : 'Pendente'}
                        </span>`;
                    }
                },
            ]
        });
    });
</script>
@endpush
