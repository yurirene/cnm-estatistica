@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Produtos'
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
                    @if (!isset($produto))
                    {!! Form::open(['url' => route('dashboard.produtos.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($produto, ['url' => route('dashboard.produtos.update', $produto->id), 'method' => 'PUT']) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text('nome', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder' => 'Camisa']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('valor', 'Valor') !!}
                                {!! Form::text('valor', null, ['class' => 'form-control isMoney', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($produto) ? 'Atualizar' : 'Cadastrar')}}</button>
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
