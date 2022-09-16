@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Demandas'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário de Demandas</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    @if (!isset($demanda))
                        @if(!isset($campos))
                        {!! Form::open(['method' => 'POST', 'route' => 'dashboard.demandas.informacoesAdicionais', 'files' => true]) !!}
                        @else
                        {!! Form::open(['method' => 'POST', 'route' => 'dashboard.demandas.store', 'files' => true]) !!}
                        @endif
                    @else
                    {!! Form::model($demanda, ['url' => route('dashboard.demandas.update', $demanda->id), 'method' => 'PUT']) !!}
                    @endif

                    @if(isset($campos))
                    <h2>Informações Extras</h2>
                    @endif
                    <div class="row">

                        @if(!isset($campos))
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    {!! Form::label('titulo', 'Título') !!}
                                    {!! Form::text('titulo', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                                </div>
                            </div>
                            @if(!isset($demanda))
                            <div class="col-md-5 mt-2">
                                <div class="form-group{{ $errors->has('arquivo') ? ' has-error' : '' }}">
                                    {!! Form::label('arquivo', 'Planilha') !!}
                                    {!! Form::file('arquivo', ['required' => 'required', 'class' => 'form-control']) !!}
                                    <small class="text-danger">{{ $errors->first('arquivo') }}</small>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if(isset($campos))
                            @foreach($campos as $key => $campo)
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    {!! Form::label('campo['. $key .']', 'Equivalente: '. $campo) !!}
                                    {!! Form::select('campo['. $key .']', $usuarios, null, ['class' => 'form-control', 'required'=>true, 'placeholder' => 'Selecione']) !!}
                                </div>
                            </div>
                            @endforeach
                            {!! Form::hidden('path', $path) !!}
                            {!! Form::hidden('titulo', $titulo) !!}
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <button class="btn btn-success">Enviar</button>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
