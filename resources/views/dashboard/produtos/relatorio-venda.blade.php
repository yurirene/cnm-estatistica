<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th, .table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        .footer {
            position: fixed;
            bottom: 0;
            font-size: 10px;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Relatório de {{ $titulo }}</h2>
        <p>Gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produtos</th>
                <th>Valor Unitário</th>
                <th>Valor Compra</th>
                <th>Forma Pagamento</th>
                <th>Vendedor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dados['pedidos'] as $pedido)
                <tr>
                    <td>{{ $pedido['data'] }}</td>
                    <td>
                        @foreach($pedido['produtos'] as $produto)
                            {{ $produto['quantidade'] }}und - {{ $produto['nome'] }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($pedido['produtos'] as $produto)
                            R${{ number_format($produto['valor'], 2, ',', '.') }}<br>
                        @endforeach
                    </td>
                    <td>{{ number_format($pedido['valor_pedido'], 2, ',', '.') }}</td>
                    <td>{{ $pedido['pagamento'] }}</td>
                    <td>{{ $pedido['vendedor'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Página <span class="pagenum"></span>
    </div>
    <table>
        <tr>
            <td style="">
                <b>Total de Vendas:</b> {{ $dados['total_pedidos'] }} pedidos
                <br>
                <ul>
                    @foreach($dados['produtos_vendidos'] as $produto)
                        <li>
                            {{ $produto['nome'] }}: {{ $produto['quantidade'] }} unidade{{ $produto['quantidade'] > 1 ? 's' : '' }}

                        </li>
                    @endforeach
                </ul>
                <b>Total Recebido:</b> R${{ number_format($dados['total_pago'], 2, ',', '.') }}
                <br>
                <ul>
                    @foreach($dados['formas_pagamento'] as $forma => $total)
                        <li>{{ $forma }}: R${{ number_format($total, 2, ',', '.') }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
    </table>
</body>
</html>
