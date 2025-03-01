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
                    @if (!isset($lancamento))
                    {!! Form::open(['url' => route('dashboard.apps.tesouraria.store'), 'method' => 'POST', 'files' => true]) !!}
                    @else
                    {!! Form::model($lancamento, ['url' => route('dashboard.apps.tesouraria.update', $lancamento->id), 'method' => 'PUT', 'files' => true]) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('descricao', 'Descrição') !!}
                                {!! Form::text('descricao', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('tipo', 'Tipo') !!}
                                {!! Form::select('tipo', $tipos, null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('valor', 'Valor') !!}
                                {!! Form::text('valor', null, ['class' => 'form-control isMoney', 'required' => true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('data_lancamento', 'Data do Lançamento') !!}
                                {!! Form::date('data_lancamento', null,  ['class' => 'form-control', 'required'=>true]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('categoria_id', 'Categoria') !!}
                                {!! Form::select(
                                    'categoria_id',
                                    ["" => 'Sem categoria'] + $categorias,
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'required' => false,
                                        'autocomplete' => 'off'
                                    ]
                                ) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('comprovante', 'Comprovante') !!}

                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input"
                                        id="comprovante"
                                        aria-describedby="inputenviarimg"
                                        name="comprovante"
                                    >
                                    <label class="custom-file-label" for="image">Buscar Comprovante</label>
                                </div>

                                @if(!empty($lancamento->comprovante))
                                <small class="text-danger">Somente se for alterar o arquivo</small>
                                <br>
                                <a href="/{{ $lancamento->comprovante }}" target="_blank">
                                    <i class="fas fa-external-link-alt"></i>
                                    Visualizar comprovante
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success">
                        <i class='fas fa-save'></i>
                        {{(isset($lancamento) ? 'Atualizar' : 'Cadastrar')}}
                    </button>
                    <a href="{{ route('dashboard.apps.tesouraria.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@push('js')
<script>

</script>
@endpush
