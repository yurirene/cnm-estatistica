@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'UMP Local'
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
                        @if(Auth::user()->federacoes->count()>1)
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('federacao_id', 'Federação') !!}
                                {!! Form::select('federacao_id', $federacoes, null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @else
                            {!! Form::hidden('federacao_id', auth()->user()->federacoes->first()->id) !!}
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('email_usuario', 'E-mail do Usuário') !!}
                                {!! Form::email('email_usuario', isset($local) ? FormHelper::getUsarioInstancia($local, 'email') : null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('nome_usuario', 'Nome do Usuário') !!}
                                {!! Form::text('nome_usuario', isset($local) ? FormHelper::getUsarioInstancia($local, 'name') : null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @if(isset($local))
                        <div class="col-md-4">
                            <div class="form-group mt-5">
                                <div class="checkbox{{ $errors->has('resetar_senha') ? ' has-error' : '' }}">
                                    <label for="resetar_senha">
                                    {!! Form::checkbox('resetar_senha', '1', null, ['id' => 'resetar_senha']) !!} Resetar Senha
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group mt-5">
                                <label for="resetar_senha">
                                {!! Form::checkbox('outro_modelo', '1', null, ['id' => 'outro_modelo']) !!} Não trabalha com modelo UMP
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