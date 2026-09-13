@extends('layouts.app')

@section('content')
@include('dashboard.partes.head', [
    'titulo' => 'Início',
    'url_tutorial' => config('tutoriais.index.federacao'),
    'remover' => true,
])
@include('dashboard.index.federacao.cards', [
    'totalizadores' => DashboardHelper::getTotalizadores(),
])

@php
    $federacao = DashboardHelper::getInfo();
    $game = DashboardHelper::getGamificacao();
@endphp

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="mb-0">Informações</h2>
                        <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#modalEditar">
                            Editar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-row"><span class="k">NOME</span><span class="v">{{ $federacao->nome }}</span></div>
                        <div class="info-row"><span class="k">PRESBITÉRIO</span><span class="v">{{ $federacao->presbiterio }}</span></div>
                        <div class="info-row"><span class="k">DATA DE ORGANIZAÇÃO</span><span class="v">{{ $federacao->data_organizacao_formatada }}</span></div>
                        <div class="info-row"><span class="k">REDES SOCIAIS</span><span class="v">{{ $federacao->midias_sociais }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-5 mb-xl-0">
            @include('dashboard.index.avisos', ['game' => $game])
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mt-3">
            @include('dashboard.index.partes.analise-estatistica')
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mt-3">
            @include('dashboard.index.gamificacao.tabela-entrega', [
                'game' => $game,
                'colunaNome' => 'UMP Local',
            ])
        </div>
    </div>
</div>

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
                {!! Form::text(
                    'data_organizacao',
                    $federacao->data_organizacao_formatada,
                    ['class' => 'form-control isDate']
                ) !!}
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
                            imprimir = `<a href="${rotaExport.replace(':id', result.id)}" class="btn btn-sm btn-primary" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>`;
                        }
                        return imprimir || '—';
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
                {
                    data: 'impacto',
                    render: function (data, type, result) {
                        return result.impacto
                            ? `<span style="color:var(--color-bad);font-weight:600;">${result.impacto}</span>`
                            : '—';
                    }
                }
            ]
        });
    });
</script>
@endpush
