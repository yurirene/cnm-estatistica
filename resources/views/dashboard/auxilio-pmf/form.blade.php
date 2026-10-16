@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Auxílio PMF',
    'subtitulo' => isset($edital) ? 'Editar edital' : 'Novo edital',
    'botaoRetorno' => route('dashboard.auxilio-pmf.index'),
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ isset($edital) ? 'Editar edital' : 'Cadastrar edital' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (!isset($edital))
                    {!! Form::open([
                        'method' => 'POST',
                        'route' => 'dashboard.auxilio-pmf.store',
                    ]) !!}
                    @else
                    {!! Form::model($edital, [
                        'url' => route('dashboard.auxilio-pmf.update', $edital->id),
                        'method' => 'PUT',
                    ]) !!}
                    @endif

                    <div class="row">
                        <div class="col-md-6 mt-2">
                            <div class="form-group">
                                {!! Form::label('titulo', 'Título') !!}
                                {!! Form::text('titulo', null, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'autocomplete' => 'off',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3 mt-2">
                            <div class="form-group">
                                {!! Form::label('ano', 'Ano') !!}
                                {!! Form::number('ano', isset($edital) ? null : date('Y'), [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'min' => 2000,
                                    'max' => 2100,
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3 mt-2">
                            <div class="form-group">
                                {!! Form::label('data_limite_proposta', 'Data limite da proposta') !!}
                                {!! Form::date(
                                    'data_limite_proposta',
                                    isset($edital) ? $edital->data_limite_proposta->format('Y-m-d') : null,
                                    [
                                        'class' => 'form-control',
                                        'required' => true,
                                    ]
                                ) !!}
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-group">
                                {!! Form::label('link_edital', 'Link do Edital (Google Drive)') !!}
                                {!! Form::url('link_edital', null, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'placeholder' => 'https://drive.google.com/...',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-group">
                                {!! Form::label('link_proposta', 'Link da Proposta (Google Drive)') !!}
                                {!! Form::url('link_proposta', null, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'placeholder' => 'https://drive.google.com/...',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-group">
                                {!! Form::label('link_relatorio', 'Link do Relatório de prestação de contas (Google Drive)') !!}
                                {!! Form::url('link_relatorio', null, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'placeholder' => 'https://drive.google.com/...',
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col text-right">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
