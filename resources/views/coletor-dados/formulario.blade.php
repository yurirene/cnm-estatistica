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
                        {!! Form::open(['method' => 'POST', 'url' => route("coletor-dados.responder", ['id' => $formulario->id])]) !!}

                        <div class="row">
                            <div class="col">
                                <h2>
                                    <b>UMP Local:</b> {{ $local->nome }}
                                </h2>
                                <h2>
                                    <b>Status: </b> <span class="badge bg-{{ $formulario->status ? 'sucesso' : 'danger' }}">{{ $formulario->status ? 'Respondido' : 'Sem Resposta' }}</span>
                                </h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div>
                                    <div class="form-group mt-3">
                                        <label>Tipo de Sócio</label>
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            id="ativos"
                                            name="tipo"
                                            autocomplete="off"
                                            value="ativos"
                                            {{ !empty($resposta) && $resposta['tipo'] == "ativos" ? 'checked' : '' }}
                                        >
                                        <label class="btn btn-outline-primary" for="ativos">Ativo</label>
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            id="cooperadores"
                                            name="tipo"
                                            autocomplete="off"
                                            value="cooperadores"
                                            {{ !empty($resposta['tipo']) && $resposta['tipo'] == "cooperadores" ? 'checked' : '' }}
                                        >
                                        <label class="btn btn-outline-primary" for="cooperadores">Cooperador</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="ano_nascimento">Ano de Nascimento</label>
                                        {!! Form::text(
                                            'ano_nascimento',
                                            !empty($resposta['ano_nascimento']) ? $resposta['ano_nascimento'] : null,
                                            [
                                                'class' => 'form-control isYear',
                                                'autocomplete' => 'off',
                                                'required' => 'required',
                                                'placeholder' => '2000'
                                            ]
                                        ) !!}
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Sexo</label>
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            id="masculino"
                                            name="sexo"
                                            autocomplete="off"
                                            value="homens"
                                            {{ empty($resposta['sexo']) || $resposta['sexo'] == "homens" ? 'checked' : '' }}
                                        >
                                        <label class="btn btn-outline-primary" for="masculino">Masculino</label>
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            id="feminino"
                                            name="sexo"
                                            autocomplete="off"
                                            value="mulheres"
                                            {{ !empty($resposta['sexo']) && $resposta['sexo'] == "mulheres" ? 'checked' : '' }}
                                        >
                                        <label class="btn btn-outline-primary" for="feminino">Feminino</label>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="estado_civil">Estado Civil</label>
                                        {!! Form::select(
                                            'estado_civil',
                                            $estadosCivis,
                                            !empty($resposta['estado_civil']) ? $resposta['estado_civil'] : null,
                                            ['class' => 'form-control']
                                        ) !!}
                                    </div>

                                    <div class="form-group mt-3">
                                        <label>Tem Filhos?</label>
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            id="sim_filhos"
                                            name="filhos"
                                            autocomplete="off"
                                            value="1"
                                            {{ !empty($resposta['filhos']) && $resposta['filhos'] == "1" ? 'checked' : '' }}
                                        >
                                        <label class="btn btn-outline-primary" for="sim_filhos">Sim</label>
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            id="nao_filhos"
                                            name="filhos"
                                            autocomplete="off"
                                            value="0"
                                            {{ empty($resposta['filhos']) || $resposta['filhos'] != "1" ? 'checked' : '' }}
                                        >
                                        <label class="btn btn-outline-primary" for="nao_filhos">Não</label>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="escolaridade">Escolaridade</label>
                                        {!! Form::select(
                                            'escolaridade',
                                            $escolaridades,
                                            !empty($resposta['escolaridade']) ? $resposta['escolaridade'] : null,
                                            ['class' => 'form-control', 'required' => true]
                                        ) !!}
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="deficiencia">Possui alguma deficiência?</label>
                                        {!! Form::select(
                                            'deficiencia[]',
                                            $deficiencias,
                                            !empty($resposta['deficiencia']) ? $resposta['deficiencia'] : null,
                                            [
                                                'class' => 'form-control isSelect2',
                                                "multiple" => "multiple"
                                            ]
                                        ) !!}  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-left">
                                    <button type="submit" class="btn btn-primary">Enviar</button>
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