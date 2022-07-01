@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Formulários - UMP Local'
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
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(count($anos) > 0)
        @include('dashboard.formularios.federacao.respostas')
    @endif
    @if($coleta)
    <div class="row mt-5" id="formulario_ump" style="display: none;">
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
                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.formularios-federacoes.store', 'class' => 'form-horizontal']) !!}

                    <h3>Dados obtidos do Relatório Estatístico das UMPs Locais</h3>
                    @include('dashboard.formularios.federacao.totalizador')

                    <hr class="my-3">
                    
                    <h3>Programações</h3>
                    @include('dashboard.formularios.federacao.programacoes')


                    <hr class="my-3">

                    <h3>ACI</h3>
                    @include('dashboard.formularios.federacao.aci')

                    @if(count(auth()->user()->federacoes) > 1)
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('federacao_id', 'Federação') !!}
                                {!! Form::select('federacao_id', auth()->user()->federacoes->pluck('sigla', 'id'), null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @else 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::hidden('federacao_id', auth()->user()->federacoes()->first()->id ,['id' => 'federacao_id', 'class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
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
    @endif
</div>  
@endsection

@push('js')

@include('dashboard.formularios.federacao.js.script')

@endpush