@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Formulários - Sinodal'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Painel Estatístico</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-inline">
                                @if(count($anos) > 0)
                                <div class="form-group mb-2">
                                    {!! Form::label('Ano') !!}
                                    {!! Form::select('ano', $anos, null, ['class' => 'form-control ml-1', 'id' => 'ano']) !!}
                                </div>
                                <button type="button" id="visualizar" class="btn btn-primary mb-2 ml-3">Visualizar</button>
                                @endif
                                @if($coleta)
                                    <button type="button" id="responder" class="btn btn-primary mb-2 ml-1">Responder</button>
                                    <button type="button" id="importar" class="btn btn-primary mb-2 ml-1">Importar Excel</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.formularios.sinodal.respostas')
    @if($coleta)
    <div class="row mt-5" id="formulario_ump" style="{{ $errors->has('somatorio') ? ' ' : 'display: none;' }}">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário Estatístico</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @error('somatorio')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror


                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.formularios-sinodais.store', 'class' => 'form-horizontal']) !!}

                    <h3>Estrutura</h3>
                    @include('dashboard.formularios.sinodal.federacoes-umps')

                    <hr class="my-3">
                    
                    <h3>Perfil do Sócio</h3>
                    @include('dashboard.formularios.sinodal.perfil')
                    
                    <hr class="my-3">

                    <h3>Estado Civil</h3>
                    @include('dashboard.formularios.sinodal.estado_civil')

                    <hr class="my-3">

                    <h3>Escolaridade</h3>
                    @include('dashboard.formularios.sinodal.escolaridade')

                    <hr class="my-3">

                    <h3>Deficiência</h3>
                    @include('dashboard.formularios.sinodal.deficiencia')

                    <hr class="my-3">

                    <h3>Programações</h3>
                    @include('dashboard.formularios.sinodal.programacoes')

                    <hr class="my-3">

                    <h3>ACI</h3>
                    @include('dashboard.formularios.sinodal.aci')

                    @if(count(auth()->user()->sinodais) > 1)
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sinodal_id', 'Sinodal') !!}
                                {!! Form::select('sinodal_id', auth()->user()->sinodais->pluck('nome', 'id'), null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @else 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::hidden('sinodal_id', auth()->user()->sinodais()->first()->id ,['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    @endif

                    <div class="btn-group pull-right">
                    {!! Form::submit('Enviar', ['class' => 'btn btn-success']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5" id="formulario_importar" style="{{ $errors->has('somatorio') ? ' ' : 'display: none;' }}">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Importar Formulário Formulário Estatístico</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.formularios-sinodais.importar', 'class' => 'form-horizontal', 'files' => true]) !!}
                    <div class="form-group{{ $errors->has('planilha') ? ' has-error' : '' }}">
                    {!! Form::label('planilha', 'Planilha') !!}
                    {!! Form::file('planilha', ['required' => 'required', 'class' => 'form-control']) !!}
                    <p class="help-block">Selecione o arquivo</p>
                    <small class="text-danger">{{ $errors->first('planilha') }}</small>
                    </div>

                    <div id="campos-federacoes"></div>

                    <h2>Informações Complementares</h2>
                    <h3>Programações</h3>
                    @include('dashboard.formularios.local.programacoes')

                    @if(count(auth()->user()->sinodais) > 1)
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sinodal_id', 'Sinodal') !!}
                                {!! Form::select('sinodal_id', auth()->user()->sinodais->pluck('nome', 'id'), null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @else 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::hidden('sinodal_id', auth()->user()->sinodais()->first()->id ,['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    @endif


                    <div class="btn-group pull-right">
                        
                    {!! Form::button('Importar', ['class' => 'btn btn-success', 'id' => 'botao-validar']) !!}
                    {!! Form::submit('Enviar', ['class' => 'btn btn-success']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>  
@endsection

@push('js')

@include('dashboard.formularios.sinodal.js.script')

@endpush