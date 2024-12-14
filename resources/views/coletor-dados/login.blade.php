@extends('layouts.app-externo', ['class' => 'bg-default'])

@section('content')
    <div class="header bg-gradient-primary py-7 py-lg-8">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-5 col-md-6">
                        <h1 class="text-white">Coletor Dados CNM</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt--8 pb-5">
        <!-- Table -->
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        {!! Form::open(['method' => 'GET', 'url' => route("coletor-dados.externo")]) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('codigo', 'Código do Formulário') !!}
                                    {!! Form::text('codigo', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-left">
                                    <button type="submit" class="btn btn-primary">Abrir</button>
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
