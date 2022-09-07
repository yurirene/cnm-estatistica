@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Produtos - Consignação'
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
                    @if (!isset($consignado))
                    {!! Form::open(['url' => route('dashboard.consignacao-produtos.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($consignado, ['url' => route('dashboard.consignacao-produtos.update', $consignado->id), 'method' => 'PUT']) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('produto_id', 'Produto') !!}
                                {!! Form::select('produto_id', $produtos, null , ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder'=>'Selecione um Produto']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('user_id', 'Usuário') !!}
                                {!! Form::select('user_id', $usuarios, null , ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder'=>'Selecione um Usuário']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('quantidade_consignada', 'Quantidade Consignada') !!}
                                {!! Form::number('quantidade_consignada', null, ['class' => 'form-control', 'step' => 1, 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    @if(isset($consignado))
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('quantidade_retornada', 'Quantidade Retornada') !!}
                                {!! Form::number('quantidade_retornada', null, ['class' => 'form-control', 'step' => 1, 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($consignado) ? 'Atualizar' : 'Cadastrar')}}</button>
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
