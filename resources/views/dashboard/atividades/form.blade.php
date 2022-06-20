@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Atividades',
])
    
<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (!isset($atividade))
                    {!! Form::open(['url' => route('dashboard.atividades.store'), 'method' => 'POST', 'files' => true]) !!}
                    @else
                    {!! Form::model($atividade, ['url' => route('dashboard.atividades.update', $atividade->id), 'method' => 'PUT', 'files' => true]) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('titulo', 'Nome') !!}
                                {!! Form::text('titulo', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('start', 'Data') !!}
                                {!! Form::text('start', isset($atividade) ? $atividade->start->format('d/m/Y') : null, ['class' => 'form-control isDate', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('status', 'Status') !!}
                                {!! Form::select('status', ['I' => 'Pendente', 'A' => 'Presente'], isset($atividade) ? ($atividade->status == true ? 'A' : 'I') : null , ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('tipo', 'Tipo') !!}
                                {!! Form::select('tipo', $tipos ,null , ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('observacoes', 'Observações') !!}
                                {!! Form::textarea('observacoes', null, ['class' => 'form-control', 'required'=>false, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group">
                                    {!! Form::label('imagem', 'Imagem') !!}
                                    {!! Form::file('imagem', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                @if(isset($atividade) && !is_null($atividade->imagem))
                                <a href="{{ $atividade->imagem }}" class="link"><i class="fas fa-eye mr-2"></i> Visualizar Imagem</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($atividade) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.atividades.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection