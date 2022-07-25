@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Usuários'
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
                    @if (!isset($usuario))
                    {!! Form::open(['url' => route('dashboard.usuarios.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($usuario, ['url' => route('dashboard.usuarios.update', $usuario->id), 'method' => 'PUT']) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('name', 'Nome') !!}
                                {!! Form::text('name', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('email', 'Email') !!}
                                {!! Form::email('email', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'readonly' => false]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('perfil_id[]', 'Perfil') !!}
                                {!! Form::select('perfil_id[]', $perfis, isset($usuario) ? $usuario->roles->pluck('id') : null, ['class' => 'form-control isSelect2', 'required'=> false, 'autocomplete' => 'off', 'multiple' => true]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @can('isAdmin')
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('regiao_id[]', 'Região') !!}
                                {!! Form::select('regiao_id[]', $regioes, isset($usuario) ? $usuario->regioes->pluck('id') : null, ['class' => 'form-control isSelect2', 'required'=> false, 'autocomplete' => 'off', 'multiple' => true]) !!}
                            </div>
                        </div>
                        @endcan
                        @if(isset($usuario))
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('status', 'Situação') !!}
                                {!! Form::select('status', ['A' => 'Ativa', 'I' => 'Inativa'],  isset($usuario) ? ($usuario->status == true ? ' A' : 'I') : null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($usuario) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.usuarios.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                    @if (isset($usuario))
                    {!! Form::open(['method' => 'POST', 'url' => route("dashboard.usuarios.reset-senha", $usuario->id), "class" => 'mt-5']) !!}
                    <div class="btn-group pull-right">
                    {!! Form::submit('Resetar Senha', ['class' => 'btn btn-warning']) !!}
                    </div>
                    {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection