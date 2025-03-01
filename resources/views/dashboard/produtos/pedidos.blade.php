@extends('layouts.app')

@section('content')


@include('dashboard.partes.head', [
    'remover' => true,
    'titulo' => 'Pedidos'
])
<div class="container-fluid mt-3">
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
                            <div class="input-group mb-3">
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
     <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    Lista de Pedidos para Finalizar
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Pedido</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Pagamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidos as $pedido)
                            <tr>
                                <td>
                                    @if(!$pedido['status'])
                                    <button class="btn btn-sm btn-success" onclick="pagar('{{$pedido['id']}}', {{ $pedido['forma_pagamento'] }})">
                                        <i class="fas fa-check"></i>
                                        Pagar
                                    </button>

                                    <button class="btn btn-sm btn-danger" onclick="cancelar('{{$pedido['id']}}')">
                                        <i class="fas fa-trash"></i>
                                        Cancelar
                                    </button>
                                    @endif
                                </td>
                                <td>{{ $pedido['nome'] }} </td>
                                <td>
                                    @foreach($pedido['produtos'] as $produto)
                                        {{ $produto }} <br>
                                    @endforeach
                                </td>
                                <td>{{ $pedido['valor'] }} </td>
                                <td>{{ $pedido['pagamento'] }} </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>

    const ROUTE_DELETE = "{{ route('dashboard.pedidos.cancelar', ':id') }}";
    const ROUTE_PAGAR = "{{ route('dashboard.pedidos.pagar', [':id', ':forma']) }}";

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

    function cancelar(id) {
        let route = ROUTE_DELETE.replace(':id', id);
        deleteRegistro(route);
    }
    function pagar(id, forma) {
        let route = ROUTE_PAGAR.replace(':id', id);
        
        let opcoes = [
            { id: 1, name: 'Pix' },
            { id: 2, name: 'Cartão' },
            { id: 3, name: 'Dinheiro' }
        ];

        let options = {};
        $.map(opcoes, function(o) {
            options[o.id] = o.name;
        });

        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: `Confirmar Pagamento com ${options[forma]}?`,
            input: 'select',
            inputOptions: options,
            inputValue: forma,
            showCancelButton: true,
            textCancelButton: 'Cancelar',
            confirmButtonText: 'Pagar',
            animation: 'slide-from-top',
            inputPlaceholder: 'Selecione a forma de pagamento'
        }).then(function (result) {
            if (result.isConfirmed) {
                route = route.replace(':forma', result.value);
                window.location.href = route;
            } 
        });
    }

</script>


@endpush
