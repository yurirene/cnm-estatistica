@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Comissão Executiva'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário da Reunião da Comissão Executiva</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    @if (!isset($reuniao))
                    {!! Form::open([
                        'method' => 'POST',
                        'route' => 'dashboard.comissao-executiva.store',
                    ]) !!}
                    @else
                    {!! Form::model(
                        $reuniao,
                        [
                            'url' => route('dashboard.comissao-executiva.update', $reuniao->id),
                            'method' => 'PUT'
                        ]
                    ) !!}
                    @endif

                    <div class="row">
                        <div class="col-md-3 mt-2">
                            <div class="form-group">
                                {!! Form::label('ano', 'Ano') !!}
                                {!! Form::text(
                                    'ano',
                                    null,
                                    [
                                        'class' => 'form-control isYear',
                                        'required' => true,
                                        'autocomplete' => 'off'
                                    ]
                                ) !!}
                            </div>
                        </div>
                        <div class="col-md-3 mt-2">
                            <div class="form-group">
                                {!! Form::label('local', 'Local') !!}
                                {!! Form::text(
                                    'local',
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'required' => true,
                                        'autocomplete' => 'off'
                                    ]
                                ) !!}
                            </div>
                        </div>

                        <div class="col-md-2 mt-2">

                            <span>Receber documentos</span>
                            <br>
                            <input type="checkbox"
                                class="parametro"
                                data-toggle="toggle"
                                data-onstyle="success"
                                data-on="Aberto"
                                data-id="aberto"
                                data-off="Fechado"
                                name="aberto"
                                id="aberto"
                                style="margin-top:20px;"
                                {{ isset($reuniao) && $reuniao->aberto == 1 ? 'checked' : ''}}
                            >
                        </div>
                        <div class="col-md-2 mt-2">

                            <span>Solicitar Diretoria</span>
                            <br>
                            <input type="checkbox"
                                class="parametro"
                                data-toggle="toggle"
                                data-onstyle="success"
                                data-on="Receber"
                                data-id="diretoria"
                                data-off="Não Receber"
                                name="diretoria"
                                id="diretoria"
                                style="margin-top:20px;"
                                {{ isset($reuniao) && $reuniao->diretoria == 1 ? 'checked' : ''}}
                            >
                        </div>
                        <div class="col-md-2 mt-2">

                            <span>Solicitar Relatorio Estatistico</span>
                            <br>
                            <input type="checkbox"
                                class="parametro"
                                data-toggle="toggle"
                                data-onstyle="success"
                                data-on="Receber"
                                data-id="relatorio_estatistico"
                                data-off="Não Receber"
                                name="relatorio_estatistico"
                                id="relatorio_estatistico"
                                style="margin-top:20px;"
                                {{ isset($reuniao) && $reuniao->relatorio_estatistico == 1 ? 'checked' : ''}}
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button class="btn btn-success">
                                    {{ isset($reuniao) ? 'Atualizar' : 'Cadastrar' }}
                                </button>

                                @if(isset($reuniao) && $reuniao->status)
                                <a
                                    href="{{ route('dashboard.comissao-executiva.encerrar', $reuniao->id) }}"
                                    class="btn btn-danger"
                                    onclick="confirmar(this)"
                                >
                                    Encerrar Reunião
                                </a>
                                @endif
                                <a
                                    href="{{ route('dashboard.comissao-executiva.index') }}"
                                    class="btn btn-secondary"
                                >
                                    Voltar
                                </a>
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
