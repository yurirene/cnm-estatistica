@extends('layouts.app')

@section('content')
@include('dashboard.index.sinodal.cards',[
    'totalizador' => DashboardHelper::getTotalizadores()
])

@php $sinodal = DashboardHelper::getInfo(); @endphp

<div class="container-fluid mt--7">
    
    <div class="row">
        <div class="col-xl-3 mt-3">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class=" mb-0">Ranking</h2>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow" style="height: 100px; width: 100px">
                                <i class="fas fa-medal" style="font-size: 50px;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h2 class="text-center"><span class="badge badge-primary h2" >X°</span></h2>
                            <h5 class="text-center">Posição</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mt-3">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class=" mb-0">Informações</h2>
                        </div>
                        <div class="col">
                            <ul class="nav nav-pills justify-content-end">
                                <li class="nav-item mr-2 mr-md-0">
                                    <a href="#" class="nav-link py-2 px-3 active"  data-toggle="modal" data-target="#modalEditar">
                                        <span class="d-none d-md-block">Editar</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3><span class="badge badge-primary">Nome:</span> {{ $sinodal->nome }}</h3>
                            <h3><span class="badge badge-primary">Sínodo:</span> {{ $sinodal->sinodo }}</h3>
                            <h3><span class="badge badge-primary">Data de Organização:</span> {{ $sinodal->data_organizacao_formatada }}</h3>
                            <h3><span class="badge badge-primary">Redes Sociais:</span> {{ $sinodal->midias_sociais }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-5 mt-3 mb-5 mb-xl-0">
            @include('dashboard.index.avisos')
        </div>
       
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Informações</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::model($sinodal, ['url' => route('dashboard.sinodais.update-info', $sinodal->id), 'method' => 'PUT']) !!}
            <div class="modal-body">
                <div class="form-group{{ $errors->has('nome') ? ' has-error' : '' }}">
                {!! Form::label('nome', 'Nome') !!}
                {!! Form::text('nome', null, ['class' => 'form-control', 'required' => 'required']) !!}
                <small class="text-danger">{{ $errors->first('nome') }}</small>
                </div>
                <div class="form-group{{ $errors->has('sinodo') ? ' has-error' : '' }}">
                {!! Form::label('sinodo', 'Sínodo') !!}
                {!! Form::text('sinodo', null, ['class' => 'form-control', 'required' => 'required']) !!}
                <small class="text-danger">{{ $errors->first('sinodo') }}</small>
                <div class="form-group{{ $errors->has('data_organizacao') ? ' has-error' : '' }}">
                {!! Form::label('data_organizacao', 'Data da Organização') !!}
                {!! Form::text('data_organizacao', null, ['class' => 'form-control isDate', 'required' => 'required']) !!}
                <small class="text-danger">{{ $errors->first('data_organizacao') }}</small>
                </div>
                <div class="form-group{{ $errors->has('midias_sociais') ? ' has-error' : '' }}">
                {!! Form::label('midias_sociais', 'Mídias Sociais') !!}
                {!! Form::text('midias_sociais', null, ['class' => 'form-control']) !!}
                <small class="text-danger">{{ $errors->first('midias_sociais') }}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
