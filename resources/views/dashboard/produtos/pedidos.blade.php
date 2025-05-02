@extends('layouts.app')

@section('content')


@include('dashboard.partes.head', [
    'remover' => true,
    'titulo' => 'Pedidos'
])
<div class="container-fluid mt-3">
    @can('rota-permitida', ['dashboard.pedidos.index'])
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    Novo Pedido
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'url' => route('dashboard.pedidos.store'),
                        'method' => 'POST'
                    ]) !!}

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('vendedor', 'Nome do Vendedor') !!}
                                {!! Form::text(
                                    'vendedor',
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'autocomplete' => 'off',
                                        'required' => true
                                    ]
                                ) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('comanda', 'Comanda') !!}
                                {!! Form::number(
                                    'comanda',
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'autocomplete' => 'off',
                                        'required' => true
                                    ]
                                ) !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text(
                                    'nome',
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'autocomplete' => 'off',
                                        'required' => true
                                    ]
                                ) !!}
                            </div>
                        </div>
                    </div>                    
                    <h5>Produtos</h5>
                    <div class="row">
                        @foreach($produtos as $produto)
                        <div class="col-md-12">
                            <span class="" id="basic-addon1">{{ $produto->nome }}</span>
                            <div class="input-group">
                                {!! Form::number(
                                    "produtos[$produto->id]",
                                    0,
                                    [
                                        'class' => 'form-control produto',
                                        'autocomplete' => 'off',
                                        'min' => 0,
                                        'max' => $produto->estoque,
                                        'data-valor' => $produto->valor,
                                        'readonly' => !$produto->estoque
                                    ]
                                ) !!}
                                <span class="input-group-text">R$ {{ $produto->valor }}</span>
                            </div>
                            <small class='mb-3'>Restam: {{ $produto->estoque }}</small>
                            <hr>
                        </div>
                        @endforeach
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="total">
                            Total do Pedido: R$
                        </span>
                        <input
                            type="text"
                            class="form-control"
                            name="total_pedido"
                            id="total_pedido"
                            value="0"
                            readonly
                            style="display: block; direction: rtl;"
                        >
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('forma_pagamento', 'Forma Pagamento') !!}
                                {!! Form::select(
                                    'forma_pagamento',
                                    $formasPagamentos,
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'autocomplete' => 'off',
                                        'required' => true
                                    ]
                                ) !!}
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success">
                        <i class='fas fa-save'></i>
                        Fechar Pedido
                    </button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
    @endcan
</div>
@endsection

@push('js')
<script>

    $(document).ready(function() {
        atualizarValor();
        $('.produto').on('change', function() {
            atualizarValor()
        })
    })

    function atualizarValor() {
        let total = 0;

        $('.produto').each(function(key, item) {
            input = $(item);
            let valor = parseFloat(input.data('valor'));
            total += parseInt(input.val()) * valor;
        });

        $('#total_pedido').val(total);
    }    

    const inputVendedor = document.getElementById("vendedor");
        // Carrega valor salvo ao abrir a página
        window.addEventListener("DOMContentLoaded", () => {
        const nomeSalvo = localStorage.getItem("nome_vendedor");

        if (nomeSalvo) {
            inputVendedor.value = nomeSalvo;
        }
    });

    // Salva automaticamente quando o valor muda
    inputVendedor.addEventListener("input", () => {
        localStorage.setItem("nome_vendedor", inputVendedor.value);
    });

</script>


@endpush
