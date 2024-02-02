@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Tesouraria'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-pills" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active"
                                id="custom-tabs-four-home-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-home"
                                role="tab"
                                aria-controls="custom-tabs-four-home"
                                aria-selected="true">
                                Lançamento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                id="custom-tabs-four-profile-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-profile"
                                role="tab"
                                aria-controls="custom-tabs-four-profile"
                                aria-selected="false">
                                Categoria
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                id="custom-tabs-four-relatorio-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-relatorio"
                                role="tab"
                                aria-controls="custom-tabs-four-relatorio"
                                aria-selected="false">
                                Relatório
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div
                            class="tab-pane fade show active"
                            id="custom-tabs-four-home"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab"
                        >
                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table w-100']) !!}
                            </div>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="custom-tabs-four-profile"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-profile-tab"
                        >
ww
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<div class="modal fade" id="modal-sugestao"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modal-sugestao-label"
    aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-sugestao-label">Minhas Sugestões</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(
                    [
                        'method' => 'POST',
                        'route' => ['dashboard.helpdesk.store']
                ]) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('titulo', 'Título') !!}<small class="text-danger">*</small>
                            {!! Form::text('titulo', null, [
                                'class' => 'form-control',
                                'required'=> true,
                                'autocomplete' => 'off',
                                'placeholder' => "Síntese da sua sugestão"
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('telefone', 'Telefone') !!}
                            {!! Form::text('telefone', null, [
                                'class' => 'form-control isTelefone',
                                'required'=> false,
                                'autocomplete' => 'off',
                                'placeholder' => "Apenas se quiser"
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('descricao', 'Descrição') !!}<small class="text-danger">*</small>
                            {!! Form::textarea('descricao', null, [
                                'class' => 'form-control',
                                'required'=> true,
                                'autocomplete' => 'off',
                                'rows' => 3
                            ]) !!}
                        </div>
                    </div>
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-paper-plane"></i>
                    Enviar
                </button>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}
@endpush
