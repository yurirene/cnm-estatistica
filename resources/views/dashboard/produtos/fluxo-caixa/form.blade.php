@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Produtos - Fluxo de Caixa'
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
                    @if (!isset($fluxo))
                    {!! Form::open(['url' => route('dashboard.produtos.fluxo-caixa.store'), 'method' => 'POST', 'files' => true]) !!}
                    @else
                    {!! Form::model($fluxo, ['url' => route('dashboard.produtos.fluxo-caixa.update', $fluxo->id), 'method' => 'PUT', 'files' => true]) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('tipo', 'Tipo') !!}
                                {!! Form::select('tipo', $tipos, null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('descricao', 'Descrição') !!}
                                {!! Form::text('descricao', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder' => 'Venda de X camisas']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('valor', 'Valor') !!}
                                {!! Form::text('valor', null, ['class' => 'form-control isMoney', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>Comprovante</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input"
                                    id="comprovante" aria-describedby="inputenviarimg"
                                    name="comprovante"
                                >
                                <label class="custom-file-label" for="image">Buscar Comprovante</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @if(!empty($fluxo->comprovante))
                                <a href="/{{ $fluxo->comprovante }}" target="_blank" class="btn btn-link">
                                    <em class="fas fa-file"></em> Abrir comprovante
                                </a>
                            @endif
                        </div>
                    </div>

                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($fluxo) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.produtos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
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
