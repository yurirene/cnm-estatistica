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
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        {!! Form::open(['method' => 'POST', 'url' => '{{ route("coletor-dados.responder") }}']) !!}

                        <div class="row">
                            <div class="col">
                                <div>
                                    <div class="form-group">
                                        <label for="ano_nascimento">Ano de Nascimento</label>
                                        {!! Form::text('ano_nascimento', null, ['class' => 'form-control isYear', 'autocomplete' => 'off', 'required' => 'required', 'placeholder' => '2000']) !!}
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Sexo</label>
                                        <input type="radio" class="btn-check" id="masculino" name="sexo" autocomplete="off" value="0" required >
                                        <label class="btn btn-outline-primary" for="masculino">Masculino</label>
                                        <input type="radio" class="btn-check" id="feminino" name="sexo" autocomplete="off" value="1" >
                                        <label class="btn btn-outline-primary" for="feminino">Feminino</label>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="estado_civil">Estado Civil</label>
                                        {!! Form::select('estado_civil', $estadosCivis, null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="escolaridade">Escolaridade</label>
                                        {!! Form::select('escolaridade', $escolaridades, null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="deficiencia">Possui alguma deficiência?</label>
                                        {!! Form::select('deficiencia[]', $deficiencias, !is_null($resposta->possui_deficiencia) ? explode(',', $resposta->possui_deficiencia) : null, ['class' => 'form-control isSelect2', "multiple" => "multiple", 'required' => 'required']) !!}  
                                    </div>
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
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
