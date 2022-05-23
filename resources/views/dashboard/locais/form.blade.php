@extends('layouts.app')

@section('content')

@include('dashboard.partes.head')
    
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
                    @if (!isset($local))
                    {!! Form::open(['url' => route('dashboard.locais.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($local, ['url' => route('dashboard.locais.update', $local->id), 'method' => 'PUT']) !!}
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
                                {!! Form::label('status', 'Situação') !!}
                                {!! Form::select('status', ['A' => 'Ativa', 'I' => 'Inativa'], isset($local) ? ($local->status == true ? ' A' : 'I') : null , ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('federacao_id', 'Federação') !!}
                                {!! Form::select('federacao_id', $federacoes, null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($local) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.locais.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection