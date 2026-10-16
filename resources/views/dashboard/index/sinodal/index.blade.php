@extends('layouts.app')

@section('content')
@include('dashboard.partes.head', [
    'titulo' => 'Início',
    'url_tutorial' => config('tutoriais.index.sinodal'),
    'remover' => true,
])
@include('dashboard.index.sinodal.cards', [
    'totalizador' => DashboardHelper::getTotalizadores(),
])

@php
    $sinodal = DashboardHelper::getInfo();
    $game = DashboardHelper::getGamificacao();
@endphp

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-3 mt-3">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="mb-0">Ranking</h2>
                        @if($sinodal->ranking)
                            <button
                                type="button"
                                class="btn btn-sm btn-secondary"
                                data-container="body"
                                data-toggle="popover"
                                data-placement="top"
                                data-content="{{ $sinodal->ranking->explicacao_detalhada }}"
                            >
                                <i class="fas fa-info"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="league-medal mx-auto mb-2">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div style="font-size:26px;font-weight:800">{{ $sinodal->ranking->posicao ?? '0' }}º</div>
                    <div style="font-size:12px;color:var(--color-muted)">posição no ranking estatístico</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mt-3">
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
                        <div class="info-row"><span class="k">NOME</span><span class="v">{{ $sinodal->nome }}</span></div>
                        <div class="info-row"><span class="k">SÍNODO</span><span class="v">{{ $sinodal->sinodo }}</span></div>
                        <div class="info-row"><span class="k">DATA DE ORGANIZAÇÃO</span><span class="v">{{ $sinodal->data_organizacao_formatada }}</span></div>
                        <div class="info-row"><span class="k">REDES SOCIAIS</span><span class="v">{{ $sinodal->midias_sociais }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 mt-3 mb-5 mb-xl-0">
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
                'colunaNome' => 'Federação',
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
            {!! Form::model($sinodal, [
                'url' => route('dashboard.sinodais.update-info', $sinodal->id),
                'method' => 'PUT'
            ]) !!}
            <div class="modal-body">
                <div class="form-group{{ $errors->has('nome') ? ' has-error' : '' }}">
                    {!! Form::label('nome', 'Nome') !!}
                    {!! Form::text('nome', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    <small class="text-danger">{{ $errors->first('nome') }}</small>
                </div>
                <div class="form-group{{ $errors->has('sinodo') ? ' has-error' : '' }}">
                    {!! Form::label('sinodo', 'Sínodo') !!}
                    {!! Form::text('sinodo', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    <small class="text-danger">{{ $errors->first('sinodo') }}</small>
                </div>
                <div class="form-group{{ $errors->has('data_organizacao') ? ' has-error' : '' }}">
                    {!! Form::label('data_organizacao', 'Data da Organização') !!}
                    {!! Form::text(
                        'data_organizacao',
                        $sinodal->data_organizacao_formatada,
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
<div class="modal fade"
    id="locais-modal" tabindex="-1" role="dialog"
    aria-labelledby="locais-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="locais-modalLabel">UMPs Locais</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="locais-entregues-table" class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">UMP Local</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function() {
        var rotaExport = "{{ route('dashboard.formularios-federacao.export', ':id') }}";
        $('#formularios-entregues-table').DataTable({
            dom: 'frtip',
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.formularios-entregues", "Federacao") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        var imprimir = '';
                        if (result.entregue == 1) {
                            imprimir = `<a href="${rotaExport.replace(':id', result.id)}" class="btn btn-sm btn-primary" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>`;
                        }
                        return `<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#locais-modal" data-id="${result.id}">
                                <i class="fas fa-eye"></i>
                            </button> ${imprimir}`;
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

    $('#locais-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var route = '{{ route("dashboard.datatables.formularios-entregues", ["instancia" => "Local", "id" => ":id"]) }}'.replace(':id', id);
        $('#locais-entregues-table').DataTable().destroy();
        var rotaExport = "{{ route('dashboard.formularios-local.export', ':id') }}";
        $('#locais-entregues-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: route,
            columns: [
                {
                    render: function (data, type, result) {
                        var imprimir = '';
                        if (result.entregue == 1) {
                            imprimir = `<a href="${rotaExport.replace(':id', result.id)}" target="_blank" class="btn btn-sm btn-primary">
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
                }
            ]
        });
    });
</script>
@endpush
