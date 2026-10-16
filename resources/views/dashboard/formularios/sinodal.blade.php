@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Formulários - Sinodal',
    'url_tutorial' => config('tutoriais.estatistica.sinodal')
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
                                    {!! Form::select(
                                        'ano',
                                        $anos,
                                        null,
                                        ['class' => 'form-control ml-1', 'id' => 'ano']
                                    ) !!}
                                </div>
                                <button type="button" id="visualizar" class="btn btn-primary mb-2 ml-3">
                                    Visualizar
                                </button>
                                <a href="#" id="link_export" target="_blank" class="btn btn-primary mb-2 ml-1">
                                    Exportar
                                </a>
                                @endif
                                @if($coleta)
                                    <button type="button" id="responder" class="btn btn-primary mb-2 ml-1">
                                        <span>Responder</span>
                                        <span class="badge blob bg-danger">{{$ano_referencia}}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(count($anos) > 0)
        @include('dashboard.formularios.sinodal.respostas')
    @endif
    @if($coleta)
    <div class="row mt-5 fe-ui" id="formulario_ump" style="{{ $errors->has('somatorio') ? ' ' : 'display: none;' }}">
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
                        <div class="fe-alert">{{ $message }}</div>
                    @enderror
                    <div class="fe-year-row">
                        <label>Ano referência</label>
                        <input type="text" class="fe-control form-control" value="{{ $ano_referencia }}" disabled />
                    </div>
                    <div class="fe-divider"></div>
                    @if(!is_null($formulario))
                    {!! Form::model(
                        $formulario,
                        [
                            'route' => ['dashboard.formularios-sinodais.store'],
                            'method' => 'POST',
                            'class' => 'form-horizontal',
                            'novalidate' => true,
                            'id' => 'formulario-estatistico-form'
                        ]
                    ) !!}
                    @else
                    {!! Form::open(
                        [
                            'method' => 'POST',
                            'route' => 'dashboard.formularios-sinodais.store',
                            'class' => 'form-horizontal',
                            'novalidate' => true,
                            'id' => 'formulario-estatistico-form'
                        ]
                    ) !!}
                    @endif

                    <h4 class="fe-section-title">Dados obtidos do Relatório Estatístico das UMPs Locais</h4>
                    @include('dashboard.formularios.sinodal.totalizador')

                    <div class="fe-divider"></div>
                    <h4 class="fe-section-title">Estrutura</h4>
                    @include('dashboard.formularios.sinodal.federacoes-umps')

                    <div class="fe-divider"></div>
                    <h4 class="fe-section-title">Programações</h4>
                    @include('dashboard.formularios.sinodal.programacoes')

                    <div class="fe-divider"></div>
                    <h4 class="fe-section-title">Organização</h4>
                    @include('dashboard.formularios.sinodal.organizacao')

                    <div class="fe-divider"></div>
                    <h4 class="fe-section-title">ACI</h4>
                    @include('dashboard.formularios.sinodal.aci')

                    {!! Form::hidden(
                        'sinodal_id',
                        auth()->user()->sinodal_id,
                        [
                            'class' => 'form-control',
                            'id'=>'sinodal_id',
                            'required'=>true,
                            'autocomplete' => 'off'
                        ]
                    ) !!}

                    <div class="fe-wizard-nav">
                        <div></div>
                        <div class="fe-nav-right">
                            @if(!$formularioEntregue)
                            <button class="fe-btn fe-btn-outline" id="apenas-salvar" type="button">Apenas salvar</button>
                            @endif
                            @if(isset($notificarCE) && $notificarCE)
                            <a href="{{ route('dashboard.formularios-sinodais.notificar-ce') }}" class="fe-btn fe-btn-outline" id="notificar-ce">
                                <i class="fas fa-bullhorn"></i> Notificar
                            </a>
                            @endif
                            @if($qualidade_entrega['porcentagem'] >= $qualidade_entrega['minimo'])
                                {!! Form::submit((!isset($formulario) || !$formularioEntregue ? 'Enviar formulário' : 'Atualizar'), ['class' => 'fe-btn fe-btn-primary']) !!}
                            @else
                                <button class="fe-btn fe-btn-danger" type="button" disabled>Enviar formulário</button>
                            @endif
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/formulario-estatistico.css') }}">
@endpush

@push('js')

@include('dashboard.formularios._js.formulario')
@include('dashboard.formularios._js.resumo')
@include('dashboard.formularios.sinodal.js.script')

@endpush
