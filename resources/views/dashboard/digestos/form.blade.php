@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Digestos',
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
                    @if (!isset($digesto))
                    {!! Form::open(['url' => route('dashboard.digestos.store'), 'method' => 'POST', 'files' => true]) !!}
                    @else
                    {!! Form::model($digesto, ['url' => route('dashboard.digestos.update', $digesto->id), 'method' => 'PUT', 'files' => true]) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('titulo', 'Título') !!}
                                {!! Form::text('titulo', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('ano', 'Ano') !!}
                                {!! Form::text('ano', date('Y'), ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('tipo_reuniao_id', 'Tipo Reunião') !!}
                                {!! Form::select('tipo_reuniao_id', $tipos, null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @if(!isset($digesto))
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('arquivo', 'Imagem') !!}
                                {!! Form::file('arquivo', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('texto', 'Texto') !!}
                                {!! Form::textarea('texto', null, ['class' => 'form-control', 'rows' => 10 , 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($digesto) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.digestos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection