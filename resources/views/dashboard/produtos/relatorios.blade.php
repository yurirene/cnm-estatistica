@extends('layouts.app')

@section('content')


@include('dashboard.partes.head', [
    'remover' => true,
    'titulo' => 'Relatórios'
])
<div class="container-fluid mt-3">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    Gerar Relatório
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'url' => route('dashboard.produtos.relatorios'),
                        'method' => 'POST'
                    ]) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('periodo', 'Período') !!}
                                {!! Form::text(
                                    'periodo',
                                    null,
                                    [
                                        'class' => 'form-control isDateRange',
                                        'autocomplete' => 'off',
                                        'required' => true
                                    ]
                                ) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('tipo', 'Tipo') !!}
                                {!! Form::select(
                                    'tipo',
                                    [
                                        'vendas' => 'Vendas'
                                    ],
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'autocomplete' => 'off',
                                        'required' => true
                                    ]
                                ) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('formato', 'Formato') !!}
                                {!! Form::select(
                                    'formato',
                                    [
                                        'pdf' => 'PDF'
                                    ],
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
                        <i class='fas fa-file'></i>
                        Gerar Relatório
                    </button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>

    $(document).ready(function() {
        atualizarValor();
        $('.produto').on('change', function() {
            atualizarValor()
        });
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
