@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Sinodais'
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
                    @if (!isset($sinodal))
                    {!! Form::open(['url' => route('dashboard.sinodais.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($sinodal, ['url' => route('dashboard.sinodais.update', $sinodal->id), 'method' => 'PUT']) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text('nome', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sigla', 'Sigla') !!}
                                {!! Form::text('sigla', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('status', 'Situação') !!}
                                {!! Form::select('status', ['A' => 'Ativa', 'I' => 'Inativa'],  isset($sinodal) ? ($sinodal->status == true ? ' A' : 'I') : null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if(count(auth()->user()->regioes) > 1)
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('regiao_id', 'Região') !!}
                                {!! Form::select('regiao_id', auth()->user()->regioes->pluck('nome', 'id'), null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @else 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::hidden('regiao_id', auth()->user()->regioes()->first()->id ,['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($sinodal) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.sinodais.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection