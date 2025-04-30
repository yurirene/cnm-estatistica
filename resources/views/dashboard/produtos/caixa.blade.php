@extends('layouts.app')

@section('content')


@include('dashboard.partes.head', [
    'remover' => true,
    'titulo' => 'Pedidos'
])
<div class="container-fluid mt-3">
    @can('rota-permitida', ['dashboard.pedidos.pagar'])
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
                                <th scope="col">Comanda</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Pedido</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Pagamento</th>
                                <th scope="col">Vendedor</th>
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
                                <td>{{ $pedido['comanda'] }} </td>
                                <td>{{ $pedido['nome'] }} </td>
                                <td>
                                    @foreach($pedido['produtos'] as $produto)
                                        {{ $produto }} <br>
                                    @endforeach
                                </td>
                                <td>{{ $pedido['valor'] }} </td>
                                <td>{{ $pedido['pagamento'] }} </td>
                                <td>{{ $pedido['vendedor'] }} </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection

@push('js')
<script>

    const ROUTE_DELETE = "{{ route('dashboard.pedidos.cancelar', ':id') }}";
    const ROUTE_PAGAR = "{{ route('dashboard.pedidos.pagar', [':id', ':forma']) }}";

    function cancelar(id) {
        let route = ROUTE_DELETE.replace(':id', id);
        deleteRegistro(route);
    }
    function pagar(id, forma) {
        let route = ROUTE_PAGAR.replace(':id', id);
        
        let opcoes = [
            { id: 1, name: 'Pix' },
            { id: 2, name: 'Cartão Crédito' },
            { id: 3, name: 'Cartão Débito' },
            { id: 4, name: 'Dinheiro' }
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
