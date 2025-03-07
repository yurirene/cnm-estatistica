@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Formulário Estatístico'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário Estatístico - UMP Local</h3>
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
                                        <span class="badge bg-danger blob">{{$ano_referencia}}</span>
                                    </button>
                                    <a href="{{ route('dashboard.coletor-dados.index') }}" id="coleta-dados" class="btn btn-primary mb-2 ml-1">
                                        <span class="text-white">Coletor de Dados</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.formularios.local.respostas')
    @if($coleta)
    <div class="row mt-5" id="formulario_ump" style="{{ $errors->any() ? ' ' : 'display: none;' }}">
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
                    <div class="row">
                        <div class="col-md-3">
                            Ano Referência
                            <input type="text" class="form-control" value="{{ $ano_referencia }}" disabled />
                        </div>
                    </div>
                    <hr>
                    @error('somatorio')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    @if(!is_null($formulario))
                    {!! Form::model(
                        $formulario,
                        [
                            'route' => ['dashboard.formularios-locais.store'],
                            'method' => 'POST',
                            'class' => 'form-horizontal'
                        ]
                    ) !!}
                    @else
                    {!! Form::open(
                        [
                            'method' => 'POST',
                            'route' => 'dashboard.formularios-locais.store',
                            'class' => 'form-horizontal'
                        ]
                    ) !!}
                    @endif


                    <h3>Perfil do Sócio</h3>
                    @include('dashboard.formularios.local.perfil')

                    <hr class="my-3">

                    <h3>Estado Civil</h3>
                    @include('dashboard.formularios.local.estado_civil')

                    <hr class="my-3">

                    <h3>Escolaridade</h3>
                    @include('dashboard.formularios.local.escolaridade')

                    <hr class="my-3">

                    <h3>Deficiência</h3>
                    @include('dashboard.formularios.local.deficiencia')

                    <hr class="my-3">

                    <h3>Programações</h3>
                    @include('dashboard.formularios.local.programacoes')

                    <hr class="my-3">

                    <h3>ACI</h3>
                    @include('dashboard.formularios.local.aci')
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::hidden(
                                'local_id',
                                auth()->user()->local_id,
                                ['class' => 'form-control','required'=>true, 'autocomplete' => 'off']
                            ) !!}
                        </div>
                    </div>

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

@include('dashboard.formularios.local.js.script')

@endpush
