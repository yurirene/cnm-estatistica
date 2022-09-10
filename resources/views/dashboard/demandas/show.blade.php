@extends('layouts.app')

@section('content')

@include('dashboard.demandas.cards')
@include('dashboard.partes.head', [
'titulo' => 'Demandas - Itens'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Lista de Demandas</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5><i class="fas fa-filter"></i> Filtros</h5>
                                    <div class="row mb-2">
                                        <div class="col-md-3">
                                            <label>Nível</label>
                                            {!! Form::select('nivel_filtro', $niveis, null, ['class' => 'form-control', 'id' => 'nivel_filtro', 'placeholder' => '-']) !!}
                                        </div>
                                        <div class="col-md-3">
                                            <label>Usuário</label>
                                            {!! Form::select('usuario_filtro', $usuarios, null, ['class' => 'form-control', 'id' => 'usuario_filtro', 'placeholder' => '-']) !!}
                                        </div>
                                        <div class="col-md-3">
                                            <label>Status</label>
                                            {!! Form::select('status_filtro', $status, null, ['class' => 'form-control', 'id' => 'status_filtro', 'placeholder' => '-']) !!}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-3">
                                            <button class="btn btn-primary" type="button" id="filtrar">Filtrar</button>
                                            <button class="btn btn-secondary" type="button" id="resetar">Limpar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="modal-create-item" tabindex="-1" role="dialog" aria-labelledby="modal-create-item" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-create-item" method="post" action="{{ route('dashboard.demandas.store-item', $demanda->id) }}">
                {{ csrf_field() }}
                <div class="modal-body">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('user_id', 'Usuário') !!}
                                    {!! Form::select('user_id', $usuarios, null, ['id' => 'usuario_create', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('status', 'Status') !!}
                                    {!! Form::select('status', $status, null, ['id' => 'status_create', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('nivel', 'Nivel') !!}
                                    {!! Form::select('nivel', $niveis, null, ['id' => 'nivel_create', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('documento', 'Documento') !!}
                                    {!! Form::text('documento', null, ['id' => 'documento_create', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('origem', 'Origem') !!}
                                    {!! Form::text('origem', null, ['id' => 'origem_create', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('demanda', 'Demanda') !!}
                                    {!! Form::textarea('demanda', null, ['id' => 'demanda_create', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-edit-item" tabindex="-1" role="dialog" aria-labelledby="modal-edit-item" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-item" method="post" action="">
                {{ csrf_field() }}
                <div class="modal-body">
                        <h2 id="nivel_span"></h2>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('user_id', 'Usuário') !!}
                                    {!! Form::select('user_id', $usuarios, null, ['id' => 'usuario', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('status', 'Status') !!}
                                    {!! Form::select('status', $status, null, ['id' => 'status', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('documento', 'Documento') !!}
                                    {!! Form::text('documento', null, ['id' => 'documento', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('origem', 'Origem') !!}
                                    {!! Form::text('origem', null, ['id' => 'origem', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('demanda', 'Demanda') !!}
                                    {!! Form::textarea('demanda', null, ['id' => 'demanda', 'class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<input type="hidden" id="url_post" value="{{ route('dashboard.demandas.update-item', [':demanda_id', ':item_id']) }}" />
@endsection

@push('js')

{!! $dataTable->scripts() !!}

<script>
    $('#modal-edit-item').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var item = button.data('item');
        var modal = $(this);

        let url = $('#url_post').val().replace(':demanda_id', item.demanda_id).replace(':item_id', item.id);

        modal.find('#nivel_span').html(item.nivel_formatado);
        modal.find('#usuario').val(item.user_id);
        modal.find('#status').val(item.status);
        modal.find('#documento').val(item.documento);
        modal.find('#origem').val(item.origem);
        modal.find('#demanda').val(item.demanda);

        modal.find('#form-edit-item').attr('action', url);
    })
</script>

<script>
    const table = $('#demandas-item-table');

    table.on('preXhr.dt', function(e, settings, data){
        data.nivel = $('#nivel_filtro').val();
        data.usuario = $('#usuario_filtro').val();
        data.status = $('#status_filtro').val();
    });

    $('#filtrar').on('click', function (){
        table.DataTable().ajax.reload();
        return false;
    });

    $('#resetar').on('click', function (){

        $('#nivel_filtro').val(null).trigger('change');
        $('#usuario_filtro').val(null).trigger('change');
        $('#status_filtro').val(null).trigger('change');
        table.DataTable().ajax.reload();
        return false;
    });



</script>

@endpush
