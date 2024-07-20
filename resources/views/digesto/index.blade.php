@extends('layouts.app-externo', ['class' => 'bg-default'])

@section('content')
    <div class="header bg-gradient-primary py-7 py-lg-8">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-5 col-md-6">
                        <h1 class="text-white">Digesto CNM</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="separator separator-bottom separator-skew zindex-100">
            <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
            </svg>
        </div>
    </div>

    <div class="container mt--8 pb-5">
        <!-- Table -->
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        {!! Form::open(['method' => 'GET']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('tipo_reuniao', 'Tipo da Reunião') !!}
                                    {!! Form::select('tipo_reuniao', $tipos, request()->filled('tipo_reuniao') ? request()->tipo_reuniao : null , ['id' => 'tipo_reuniao', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('ano', 'Ano') !!}
                                    {!! Form::text('ano', request()->filled('ano') ? request()->ano : null, ['class' => 'form-control', 'maxlength' => 4]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('chave', 'Palavra Chave') !!}
                                    {!! Form::text('chave', request()->filled('chave') ? request()->chave : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-left">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                        <div class="row my-4">
                            <div class="col-12">
                                <ul class="list-group">
                                    @forelse ($dados as $dado)
                                        <li class="list-group-item ">

                                            <a href="{{ route('digesto.exibir', $dado['path']) }}"
                                                target="_blank" class="btn btn-sm btn-primary"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <span class="badge badge-primary badge-pill">
                                                {{ $dado['tipo_formatado'] }}
                                            </span>
                                            {{ $dado['titulo'] }} - <i>{{ $dado['texto_formatado'] }}</i>
                                        </li>
                                    @empty
                                    <li class="list-group-item ">
                                        Sem Resultados
                                      </li>
                                    @endforelse


                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
